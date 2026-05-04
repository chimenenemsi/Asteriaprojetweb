<?php

/**
 * Assistant NutriFit — détection par mots-clés, réponses FAQ par catégories.
 * Hors API IA externe. Ne remplace pas un avis médical.
 */
class Chatbot {
    /**
     * @return array{reply: string, category: string}
     */
    public static function analyze(string $message, bool $isLoggedIn, ?string $userDisplayName): array {
        $m = self::normalize($message);
        $name = ($userDisplayName !== null && trim($userDisplayName) !== '') ? trim($userDisplayName) : null;

        if ($m === '') {
            return self::pack('aide', self::withName($name,
                'Écrivez votre question ou utilisez un bouton de suggestion sous le champ. '
                . 'Rubriques : connexion aux comptes, mot de passe, profil après connexion, erreurs ou blocages, nutrition.'));
        }

        if ($isLoggedIn
            && self::matches($m, ['erreur liste', 'impossible liste', 'back-office erreur'])) {
            return self::pack('erreurs',
                'Sur le tableau d’administration, une erreur apparaît souvent si la session expire ou une action est refusée. '
                . 'Reconnectez-vous puis réessayez. Si le problème persiste, notez le message exact.');
        }

        if (self::matches($m, ['erreur', 'bug', 'plante', 'ne marche pas', 'fonctionne pas',
            'cassé', 'erreur php', 'page blanche', 'crash', 'problème technique'])) {
            return self::pack('erreurs',
                'En cas de problème : actualisez la page, vérifiez votre connexion, réessayez dans quelques minutes. '
                . 'Pour un blocage après plusieurs erreurs sur le mot de passe, attendez le délai indiqué ou utilisez '
                . '« Mot de passe oublié » ou le reset par code secret.');
        }

        if (self::matches($m, ['compte bloqu', 'trop de tentative', 'trop dessai'])) {
            return self::pack('erreurs',
                'Après plusieurs mots de passe incorrects, l’accès peut être suspendu temporairement. '
                . 'Réessayez plus tard, passez par la récupération (e-mail ou code secret depuis la connexion), '
                . 'ou contactez un administrateur.');
        }

        if (!$isLoggedIn && self::matches($m, ['aucun compte', 'email inconnu'])) {
            return self::pack('erreurs',
                'Le message « aucun compte » signifie que cet e-mail n’est pas encore inscrit. '
                . 'Créez un compte depuis « Créer un compte », ou corrigez l’orthographe de votre adresse.');
        }

        if (!$isLoggedIn && self::matches($m, [
            'inscription', 'créer un compte', 'creer un compte', 'nouveau compte',
            "m'inscrire", 'sinscrire', 'comment sinscrire', 'creer mon compte',
        ])) {
            return self::pack('connexion',
                'Ouvrez « Créer un compte » (depuis Connexion ou index.php?action=register). Renseignez nom complet, e-mail, '
                . 'mot de passe et le code secret personnel (pour vous aider hors e-mail selon vos options). '
                . 'Après validation, connectez-vous avec le même e-mail et mot de passe.');
        }

        if (!$isLoggedIn && self::matches($m, ['code secret', 'pourquoi code secret'])) {
            return self::pack('mot_de_passe',
                'Le code secret est choisi par vous à l’inscription. Il sert avec le formulaire « Réinitialiser avec code secret » '
                . '(sur la même page que le lien mot de passe oublié). Conservez-le comme un second mot de passe personnel.');
        }

        if (!$isLoggedIn && self::matches($m, ['longueur mot', 'nombre caractères', 'règle mot passe', 'nombre de ca'])) {
            return self::pack('mot_de_passe',
                'L’application demande au moins 6 caractères pour le mot de passe à l’inscription. Pour plus de sécurité, '
                . 'combinez majuscules, minuscules, chiffres et symboles.');
        }

        if (self::matches($m, ['bonjour', 'salut', 'coucou', 'hello', 'hey', 'hi', 'bonsoir'])) {
            return self::pack('aide',
                self::withName($name,
                    $isLoggedIn
                        ? 'Bonjour ! Je vous guide dans votre espace (profil), la nutrition générale ou les erreurs fréquentes.'
                        : 'Bonjour ! Je réponds aux questions connexion ou inscription mot de passe oublie aides navigation ainsi nutrition générale.'));
        }

        if (self::matches($m, ['mot de passe', 'mdp', 'oublie'])) {
            return self::pack('mot_de_passe',
                $isLoggedIn
                    ? 'Déconnectez-vous si besoin, puis utilisez depuis Connexion « Mot de passe oublié » (lien par e-mail) '
                    . 'ou la réinitialisation par code secret.'
                    : 'Sur Connexion : « Mot de passe oublié » pour recevoir un e-mail, ou plus bas le formulaire par code secret '
                    . 'avec votre e-mail, nouveau mot de passe et code secret.');
        }

        if (self::matches($m, ['connexion', 'login', 'identifier', 'se connecter'])) {
            return self::pack('connexion',
                'Saisissez e-mail puis mot de passe et validez « Se connecter ». Nouveau ? Utilisez « Créer un compte » sur la même page.');
        }

        if (!$isLoggedIn && self::matches($m, ['accueil', 'homepage', 'où commence'])) {
            return self::pack('aide',
                'L’accueil du site liste les entrées Connexion ou Inscription. Vous pouvez aussi ouvrir directement index.php depuis votre navigateur.');
        }

        if (!$isLoggedIn && self::matches($m, ['pourquoi nutrifit', 'c est nutrifit', 'qu est-ce que nutr'])) {
            return self::pack('aide',
                'NutriFit aide à suivre une alimentation plus durable et équilibrée. Créez un compte pour accéder à votre espace après connexion ; '
                . 'cet assistant reste disponible même sans compte pour des conseils généraux.');
        }

        if ($isLoggedIn && self::matches($m, ['deconnexion', 'logout', 'quitter'])) {
            return self::pack('profil',
                'Utilisez le bouton « Se déconnecter » sur votre tableau de bord pour fermer votre session.');
        }

        if (!$isLoggedIn && self::matches($m, ['deconnexion', 'logout', 'quitter'])) {
            return self::pack('profil',
                'Après votre connexion, le bouton « Se déconnecter » apparaît sur votre tableau de bord.');
        }

        if ($isLoggedIn && self::matches($m, ['profil', 'mon nom', 'mon compte', 'dashboard', 'tableau',
            'admin', 'back-office', 'liste utilis', 'rôle utilis', 'changer role'])) {
            return self::pack('profil',
                self::withName($name,
                    'Votre espace personnel s’affiche après connexion. Les administrateurs accèdent en plus au back-office (liste des comptes, rôles, etc.). '
                    . 'Ne partagez jamais votre session ou vos codes.'));
        }

        if (!$isLoggedIn && self::matches($m, ['voir mon profil', 'informations utilisateur'])) {
            return self::pack('profil',
                'Connectez-vous d’abord : vos informations personnelles et le tableau de bord sont affichés après connexion.');
        }

        if (!$isLoggedIn && self::matches($m, ['mon compte existe', 'j ai un profil'])) {
            return self::pack('profil',
                'Si vous êtes déjà inscrit, passez par Connexion. Sinon utilisez « Créer un compte » puis reconnectez-vous.');
        }

        if (self::matches($m, ['aide', 'tutoriel', 'comment fonctionne', 'comment utiliser', 'navigation'])) {
            return self::pack('aide',
                self::withName($name,
                    $isLoggedIn
                        ? 'Une fois connecté : tableau personnel, lien de déconnexion, et cet assistant avec rubriques (connexion, mot de passe, profil, erreurs, aide, nutrition).'
                        : 'Sans compte : accueil puis Connexion ou Inscription cet assistant aide pendant les deux étapes ainsi pour la nutrition.'));
        }

        if (self::matches($m, ['nutrition durable', 'durable', 'ecolo', 'environnement'])) {
            return self::pack('nutrition',
                'Privilégiez des aliments peu transformés, des légumes variés, des céréales complètes et les produits de saison lorsque vous le pouvez.');
        }

        if (self::matches($m, ['menu', 'recette', 'repas', 'idee'])) {
            return self::pack('nutrition',
                'Assiette type : environ la moitié en légumes, un quart en protéines, un quart en féculents, plus une huile de qualité pour les lipides.');
        }

        if (self::matches($m, ['regime', 'perdre du poids', 'maigrir', 'calorie'])) {
            return self::pack('nutrition',
                'Privilégiez une évolution régulière avec sommeil, activité et alimentation variée ; un professionnel de santé cible mieux les objectifs personnels.');
        }

        if (self::matches($m, ['sport', 'musculation', 'proteine'])) {
            return self::pack('nutrition',
                'Hydratation régulière, protéines réparties dans la journée, glucides adaptés à l’intensité des séances et assiettes complètes.');
        }

        if (self::matches($m, ['merci', 'thanks', 'ok merci', 'cool merci'])) {
            return self::pack('aide', 'Avec plaisir ! Reformule ou choisis une autre catégorie (connexion mot de passe profil aide erreurs nutrition).');
        }

        if (self::matches($m, ['bye', 'au revoir', 'a bientot', 'bonne journ'])) {
            return self::pack('aide',
                self::withName($name, 'À bientôt — votre historique reste enregistré sur cet appareil tant que vous ne le videz pas.'));
        }

        return self::pack('fallback', self::withName($name, self::fallbackText($isLoggedIn)));
    }

    public static function reply(string $message, bool $isLoggedIn, ?string $userDisplayName): string {
        return self::analyze($message, $isLoggedIn, $userDisplayName)['reply'];
    }

    public static function categoryLabel(string $category): string {
        $labels = [
            'connexion' => 'Connexion',
            'mot_de_passe' => 'Mot de passe',
            'profil' => 'Profil & espace',
            'aide' => 'Aide',
            'erreurs' => 'Erreurs & blocages',
            'nutrition' => 'Nutrition',
            'fallback' => 'NutriFit',
        ];
        return $labels[$category] ?? 'Assistant';
    }

    /** @return array{reply: string, category: string} */
    private static function pack(string $category, string $reply): array {
        return ['category' => $category, 'reply' => $reply];
    }

    private static function withName(?string $displayName, string $sentence): string {
        if ($displayName === null || trim($displayName) === '') {
            return $sentence;
        }
        return trim($displayName) . ', ' . $sentence;
    }

    private static function fallbackText(bool $isLoggedIn): string {
        return $isLoggedIn
            ? 'Je n’identifie pas encore cette demande précise parmi les sujets suivants '
            . '(connexion, mot de passe, profil ou espace admin, aide au système, erreurs/blocages, nutrition générale). '
            . 'Reformula une courte phrase avec un mot-clé — par exemple tableau de bord, mot de passe oublié, ou menu équilibré.'
            : 'Je n’identifie pas encore cette demande parmi les sujets fréquent (inscription comme connexion, mot de passe, erreurs générales, aide ou nutrition). '
            . 'Une phrase courte aide : par exemple « créer mon compte », « mot de passe oublié » ou « idée repas équilibré ».';
    }

    private static function normalize(string $s): string {
        $s = trim(preg_replace('/\s+/u', ' ', $s));
        if ($s === '') {
            return '';
        }
        return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
    }

    /** @param string[] $tokens */
    private static function matches(string $normalizedInput, array $tokens): bool {
        foreach ($tokens as $t) {
            $t = self::normalize((string)$t);
            if ($t !== '' && str_contains($normalizedInput, $t)) {
                return true;
            }
        }
        return false;
    }
}
