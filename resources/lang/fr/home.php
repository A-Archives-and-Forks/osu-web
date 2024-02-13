<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

return [
    'landing' => [
        'download' => 'Télécharger maintenant',
        'online' => '<strong>:players</strong> connectés en ce moment dans <strong>:games</strong> parties',
        'peak' => 'Pic de joueurs connectés : :count ',
        'players' => '<strong>:count</strong> joueurs inscrits',
        'title' => 'bienvenue',
        'see_more_news' => 'voir plus de news',

        'slogan' => [
            'main' => 'le meilleur jeu de rythme free-to-win',
            'sub' => 'rhythm is just a click away',
        ],
    ],

    'search' => [
        'advanced_link' => 'Recherche avancée',
        'button' => 'Rechercher',
        'empty_result' => 'Aucun résultat !',
        'keyword_required' => 'Un mot clé de recherche est requis',
        'placeholder' => 'tapez pour rechercher',
        'title' => 'rechercher',

        'beatmapset' => [
            'login_required' => 'Connectez-vous pour rechercher des beatmaps',
            'more' => ':count résultats de recherche de beatmap en plus',
            'more_simple' => 'Voir plus de résultats de recherche de beatmaps',
            'title' => 'Beatmaps',
        ],

        'forum_post' => [
            'all' => 'Tous les forums',
            'link' => 'Rechercher sur le forum',
            'login_required' => 'Connectez-vous pour rechercher sur le forum',
            'more_simple' => 'Voir plus de résultats de recherche sur les forums',
            'title' => 'Forum',

            'label' => [
                'forum' => 'Rechercher dans les forums',
                'forum_children' => 'inclure les sous-forums',
                'include_deleted' => 'inclure les posts supprimés',
                'topic_id' => 'sujet #',
                'username' => 'auteur',
            ],
        ],

        'mode' => [
            'all' => 'tout',
            'beatmapset' => 'beatmap',
            'forum_post' => 'forum',
            'user' => 'joueur',
            'wiki_page' => 'wiki',
        ],

        'user' => [
            'login_required' => 'Connectez-vous pour rechercher des utilisateurs',
            'more' => ':count résultats de recherche de joueurs',
            'more_simple' => 'Voir plus de résultats de recherche de joueurs',
            'more_hidden' => 'La recherche de joueurs est limitée à :max joueurs. Essayez d\'être plus précis.',
            'title' => 'Joueurs',
        ],

        'wiki_page' => [
            'link' => 'Rechercher sur le wiki',
            'more_simple' => 'Voir plus de résultats de recherche sur le wiki',
            'title' => 'Wiki',
        ],
    ],

    'download' => [
        'action' => 'Télécharger osu!',
        'action_lazer' => 'Télécharger osu!(lazer)',
        'action_lazer_description' => 'la prochaine mise à jour majeure d\'osu!',
        'action_lazer_info' => 'consultez cette page pour plus d\'informations',
        'action_lazer_title' => 'essayez osu!(lazer)',
        'action_title' => 'télécharger osu!',
        'for_os' => 'pour :os',
        'lazer_note' => 'attention : les classements peuvent être réinitialisés',
        'macos-fallback' => 'utilisateurs macOS',
        'mirror' => 'miroir',
        'or' => 'ou',
        'os_version_or_later' => ':os_version ou plus',
        'other_os' => 'autres plateformes',
        'quick_start_guide' => 'guide de démarrage rapide',
        'tagline' => "c'est parti<br>lancez-vous !",
        'video-guide' => 'guide vidéo',

        'help' => [
            '_' => 'si vous avez des problèmes pour démarrer le jeu ou pour créer un compte, :help_forum_link ou :support_button.',
            'help_forum_link' => 'consultez le forum Help',
            'support_button' => 'contactez le support',
        ],

        'os' => [
            'windows' => 'pour Windows',
            'macos' => 'pour macOS',
            'linux' => 'pour Linux',
        ],
        'steps' => [
            'register' => [
                'title' => 'créer un compte',
                'description' => 'suivez les indications lorsque vous démarrerez le jeu pour vous connecter ou créer un nouveau compte',
            ],
            'download' => [
                'title' => 'installer le jeu',
                'description' => 'cliquez sur le bouton ci-dessus pour télécharger l\'installateur, lancez-le ensuite !',
            ],
            'beatmaps' => [
                'title' => 'obtenir des beatmaps',
                'description' => [
                    '_' => ':browse la vaste librairie de beatmaps créées par la communauté et commencez à jouer !',
                    'browse' => 'parcourez',
                ],
            ],
        ],
    ],

    'user' => [
        'title' => 'tableau de bord',
        'news' => [
            'title' => 'News',
            'error' => 'Erreur lors du chargement des news, essayez de recharger la page ?...',
        ],
        'header' => [
            'stats' => [
                'friends' => 'Amis en ligne',
                'games' => 'Parties',
                'online' => 'Utilisateurs en ligne',
            ],
        ],
        'beatmaps' => [
            'new' => 'Nouvelles beatmaps classées',
            'popular' => 'Beatmaps populaires',
            'by_user' => 'par :user',
        ],
        'buttons' => [
            'download' => 'Télécharger osu!',
            'support' => 'Soutenir osu!',
            'store' => 'osu!store',
        ],
    ],
];
