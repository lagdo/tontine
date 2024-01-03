<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tooltip Message
    |--------------------------------------------------------------------------
    |
    | Text that appears in the tooltip when the cursor hover the bubble, before
    | the popup opens.
    |
    */

    'tooltip' => 'Donner un feedback',

    /*
    |--------------------------------------------------------------------------
    | Popup Title
    |--------------------------------------------------------------------------
    |
    | This is the text that will appear below the logo in the feedback popup
    |
    */

    'title' => 'Aidez-nous à améliorer notre application',

    /*
    |--------------------------------------------------------------------------
    | Success Message
    |--------------------------------------------------------------------------
    |
    | This message will be displayed if the feedback message is correctly sent.
    |
    */

    'success' => 'Merci pour votre feedback!',

    /*
    |--------------------------------------------------------------------------
    | Placeholder
    |--------------------------------------------------------------------------
    |
    | This text will appear as the placeholder of the textarea in which the
    | the user will type his feedback.
    |
    */

    'placeholder' => 'Entrez votre feedback ici...',

    /*
    |--------------------------------------------------------------------------
    | Button Label
    |--------------------------------------------------------------------------
    |
    | Text of the confirmation button to send the feedback.
    |
    */

    'button' => 'Envoyer le feedback',

    /*
    |--------------------------------------------------------------------------
    | Feedback Texts
    |--------------------------------------------------------------------------
    |
    | Must match the feedbacks array from the config file
    |
    */
    'feedbacks' => [
        'like' => [
            'title' => "Ce que j'aime",
            'label' => "Qu'est-ce que vous aimez ?",
        ],
        'dislike' => [
            'title' => "Ce que je n'aime pas",
            'label' => "Qu'est-ce que vous n'aimez pas ?",
        ],
        'suggestion' => [
            'title' => "J'ai une suggestion",
            'label' => 'Quelle est votre suggestion ?',
        ],
        'bug' => [
            'title' => "J'ai eu un problème",
            'label' => "Expliquez ce qui s'est passé.",
        ],
    ],
];
