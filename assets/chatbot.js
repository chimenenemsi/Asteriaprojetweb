(function () {
  "use strict";

  var root = document.getElementById("nf-chatbot");
  if (!root) return;

  var toggle = root.querySelector(".nf-chat-toggle");
  var panel = root.querySelector(".nf-chat-panel");
  var form = root.querySelector(".nf-chat-form");
  var input = root.querySelector(".nf-chat-input");
  var msgs = root.querySelector(".nf-chat-messages");
  var sendBtn = root.querySelector(".nf-chat-send");
  var clearBtn = root.querySelector(".nf-chat-clear");
  var apiUrl = root.getAttribute("data-api") || "chat_api.php";

  var LS_KEY =
    "nf_chat_v3_" +
    (root.getAttribute("data-context") || "guest") +
    "_" +
    (root.getAttribute("data-user-key") || "0");
  /** @type {{role:string,text:string,ts:number,category?:string,category_label?:string}[]} */
  var conv = [];
  var MAX_STORED = 64;

  try {
    var raw = localStorage.getItem(LS_KEY);
    if (raw) {
      var parsed = JSON.parse(raw);
      if (Array.isArray(parsed)) conv = parsed.slice(-MAX_STORED);
    }
  } catch (e) {}

  function escapeHtml(s) {
    var d = document.createElement("div");
    d.textContent = s;
    return d.innerHTML;
  }

  function catClass(slug) {
    return "nf-chat-cat--" + String(slug || "fallback").replace(/[^a-z0-9_-]/gi, "");
  }

  function fmtTime(ms) {
    try {
      return new Intl.DateTimeFormat("fr-FR", {
        hour: "2-digit",
        minute: "2-digit",
      }).format(new Date(ms));
    } catch (e) {
      var dt = new Date(ms);
      return (
        dt.getHours().toString().padStart(2, "0") +
        ":" +
        dt.getMinutes().toString().padStart(2, "0")
      );
    }
  }

  function persist() {
    try {
      localStorage.setItem(LS_KEY, JSON.stringify(conv));
    } catch (e) {}
  }

  /**
   * @param {'user'|'bot'} role
   * @param {{ts?:number,category?:string,category_label?:string}} meta
   */
  function appendMessage(role, text, meta) {
    meta = meta || {};
    var ts = meta.ts != null ? meta.ts : Date.now();
    var wrap = document.createElement("article");
    wrap.className =
      "nf-chat-row nf-chat-row-" +
      role +
      (role === "user" ? " nf-chat-msg-user" : " nf-chat-msg-bot");

    var metaEl = document.createElement("div");
    metaEl.className = "nf-chat-meta-line";
    if (role === "bot" && meta.category_label) {
      var badge = document.createElement("span");
      badge.className = "nf-chat-cat " + catClass(meta.category);
      badge.textContent = meta.category_label;
      metaEl.appendChild(badge);
    }
    var time = document.createElement("time");
    time.dateTime = new Date(ts).toISOString();
    time.textContent = fmtTime(ts);
    metaEl.appendChild(time);
    wrap.appendChild(metaEl);

    var bub = document.createElement("div");
    bub.className = "nf-chat-bubble nf-chat-" + role;
    bub.innerHTML =
      '<div class="nf-chat-text">' +
      escapeHtml(text).replace(/\n/g, "<br>") +
      "</div>";
    wrap.appendChild(bub);

    msgs.appendChild(wrap);
    msgs.scrollTop = msgs.scrollHeight;

    conv.push({
      role: role,
      text: text,
      ts: ts,
      category: meta.category || undefined,
      category_label: meta.category_label || undefined,
    });
    while (conv.length > MAX_STORED) conv.shift();
    persist();
  }

  /** Affiche depuis localStorage sans repousser dans conv */
  function renderSaved() {
    msgs.innerHTML = "";
    conv.forEach(function (m) {
      var wrap = document.createElement("article");
      wrap.className =
        "nf-chat-row nf-chat-row-" +
        m.role +
        (m.role === "user" ? " nf-chat-msg-user" : " nf-chat-msg-bot");

      var metaEl = document.createElement("div");
      metaEl.className = "nf-chat-meta-line";
      if (m.role === "bot" && m.category_label) {
        var badge = document.createElement("span");
        badge.className = "nf-chat-cat " + catClass(m.category);
        badge.textContent = m.category_label;
        metaEl.appendChild(badge);
      }
      var time = document.createElement("time");
      time.dateTime = new Date(m.ts).toISOString();
      time.textContent = fmtTime(m.ts);
      metaEl.appendChild(time);
      wrap.appendChild(metaEl);

      var bub = document.createElement("div");
      bub.className = "nf-chat-bubble nf-chat-" + m.role;
      bub.innerHTML =
        '<div class="nf-chat-text">' +
        escapeHtml(m.text).replace(/\n/g, "<br>") +
        "</div>";
      wrap.appendChild(bub);
      msgs.appendChild(wrap);
    });
    msgs.scrollTop = msgs.scrollHeight;
  }

  function introCopy() {
    var ctx = root.getAttribute("data-context") || "guest_login";
    if (ctx === "user") {
      return (
        "Bienvenue. Une fois connecté vos phrases suivent automatiquement ces familles de réponses (connexion alors mot de passe comme profils administrations erreurs aides navigation nutrition). "
        + "Historique CET assistant sur CET appareil jusqu'à « Effacer »."
      );
    }
    if (ctx === "guest_register") {
      return (
        "Pendant l'inscription vous pouvez demander le rôle du code secret, les règles du mot de passe ou les erreurs fréquentes. "
        + "Touches suggestions envoyant comme FAQ automatiques l'historique demeurant CET navigateurs jusqu'à « Effacer »."
      );
    }
    return (
      "Avant connexion réponses inscription Connexion passe oubis blocées navigations nutrition FAQs automatiques "
      + "Touches suggestions historiques CET navigateurs jusqu'à « Effacer »."
    );
  }

  toggle.addEventListener("click", function () {
    var open = panel.classList.toggle("nf-chat-open");
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
    if (open) {
      if (conv.length === 0 && !root.dataset.introDone) {
        root.dataset.introDone = "1";
        appendMessage("bot", introCopy(), {
          category: "aide",
          category_label: "Aide",
        });
      }
      input.focus();
    }
  });

  if (conv.length > 0) {
    renderSaved();
    root.dataset.introDone = "1";
  }

  clearBtn.addEventListener("click", function () {
    conv = [];
    persist();
    msgs.innerHTML = "";
    delete root.dataset.introDone;
    appendMessage("bot", "Historique effacé la conversation recommence cet appareil.", {
      category: "aide",
      category_label: "Aide",
    });
  });

  form.addEventListener("submit", function (ev) {
    ev.preventDefault();
    var msg = input.value.trim();
    if (!msg) return;
    input.value = "";
    appendMessage("user", msg, {});

    sendBtn.disabled = true;
    fetch(apiUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message: msg }),
      credentials: "same-origin",
    })
      .then(function (r) {
        return r.json().then(function (data) {
          return { okHttp: r.ok, data: data };
        });
      })
      .then(function (pkg) {
        var data = pkg.data;
        if (data && data.ok && data.reply) {
          appendMessage("bot", data.reply, {
            category: data.category,
            category_label: data.category_label,
          });
        } else {
          appendMessage(
            "bot",
            (data && data.error) ||
              "Une erreur est survenue puis réessayez.",
            {
              category: "erreurs",
              category_label: "Erreurs",
            }
          );
        }
      })
      .catch(function () {
        appendMessage(
          "bot",
          "Serveur injoignable Vérifiez chat_api.php même dossiers index.php projet PHP alors.",
          {
            category: "erreurs",
            category_label: "Erreurs",
          }
        );
      })
      .finally(function () {
        sendBtn.disabled = false;
      });
  });

  root.querySelectorAll(".nf-chat-chip").forEach(function (chip) {
    chip.addEventListener("click", function () {
      var q = chip.getAttribute("data-q") || chip.textContent;
      input.value = q;
      if (chip.getAttribute("data-send") === "1") form.requestSubmit();
      else input.focus();
    });
  });
})();
