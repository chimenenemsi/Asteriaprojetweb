<?php
/**
 * Variantes contexte : guest_login | guest_register | user
 * @var string $nfChatbotContext
 */
$nfChatbotContext = $nfChatbotContext ?? 'guest_login';
$nfChatUserKey = isset($_SESSION['user']['id']) ? (string)(int)$_SESSION['user']['id'] : '0';

if ($nfChatbotContext === 'user') {
    $nfChatSubtitle = 'FAQ système Connexion passe Profil aide erreurs nutrition';
    $nfChatChips = [
        ['q' => 'Comment utiliser mon tableau de bord ?', 'label' => 'Guide espace'],
        ['q' => 'Une idée de repas équilibré ?', 'label' => 'Nutrition'],
        ['q' => 'Réinitialiser mon mot de passe', 'label' => 'Mot de passe'],
        ['q' => 'Le site affiche une erreur', 'label' => 'Erreur'],
    ];
} elseif ($nfChatbotContext === 'guest_register') {
    $nfChatSubtitle = 'Inscription code secret aide erreurs';
    $nfChatChips = [
        ['q' => 'À quoi sert le code secret ?', 'label' => 'Code secret'],
        ['q' => 'Règle sur la longueur du mot de passe', 'label' => 'Mot de passe'],
        ['q' => 'Le formulaire ne valide pas', 'label' => 'Erreur'],
        ['q' => 'Comment fonctionne cet assistant ?', 'label' => 'Aide'],
    ];
} else {
    $nfChatSubtitle = 'Connexion inscription mot de passe aides erreurs';
    $nfChatChips = [
        ['q' => 'Comment créer un compte NutriFit ?', 'label' => 'Créer un compte'],
        ['q' => 'J’ai oublié mon mot de passe', 'label' => 'Mot de passe'],
        ['q' => 'Je ne peux pas me connecter erreur blocage', 'label' => 'Erreur'],
        ['q' => 'Guide rapide du site NutriFit', 'label' => 'Aide'],
    ];
}
?>
<div id="nf-chatbot"
     class="nf-chatbot-root nf-chat-modern"
     data-api="chat_api.php"
     data-context="<?php echo htmlspecialchars($nfChatbotContext, ENT_QUOTES, 'UTF-8'); ?>"
     data-user-key="<?php echo htmlspecialchars($nfChatUserKey, ENT_QUOTES, 'UTF-8'); ?>"
     aria-live="polite">
    <button type="button" class="nf-chat-toggle" aria-expanded="false" aria-controls="nf-chat-panel-inner" aria-label="Ouvrir l’assistant NutriFit">
        <span class="nf-chat-toggle-icon">💬</span>
        <span class="nf-chat-toggle-label">Assistant</span>
    </button>
    <div id="nf-chat-panel-inner" class="nf-chat-panel" role="dialog" aria-labelledby="nf-chat-heading">
        <div class="nf-chat-toolbar">
            <div class="nf-chat-head-main">
                <span id="nf-chat-heading">Assistant NutriFit</span>
                <small><?php echo htmlspecialchars($nfChatSubtitle, ENT_QUOTES, 'UTF-8'); ?></small>
            </div>
            <div class="nf-chat-toolbar-actions">
                <button type="button" class="nf-chat-clear" title="Effacer la conversation locale" aria-label="Effacer l’historique de conversation">
                    ✕ Effacer
                </button>
            </div>
        </div>

        <div class="nf-chat-messages" tabindex="0" aria-label="Messages de la conversation"></div>

        <div class="nf-chat-chiprow-wrap">
            <p class="nf-chat-chiplabel">Suggestions</p>
            <div class="nf-chat-chiprow">
                <?php foreach ($nfChatChips as $c): ?>
                    <button type="button"
                            class="nf-chat-chip"
                            data-send="1"
                            data-q="<?php echo htmlspecialchars($c['q'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($c['label'], ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <form class="nf-chat-form" autocomplete="off">
            <input type="text" class="nf-chat-input" maxlength="2000" placeholder="Votre question…" name="nf_chat_msg" />
            <button type="submit" class="nf-chat-send nf-btn">Envoyer</button>
        </form>
        <p class="nf-chat-legal">
            Réponses automatiques FAQ — historique enregistré sur cet appareil seulement
        </p>
    </div>
</div>
<script src="assets/chatbot.js" defer></script>
