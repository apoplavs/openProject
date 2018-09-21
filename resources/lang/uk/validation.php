<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute повинен бути прийнятий.',
    'active_url'           => ':attribute не валідний URL.',
    'after'                => ':attribute повинна бути дата, що пізніше :date.',
    'after_or_equal'       => ':attribute повинна бути дата, що пізніша або рівна :date.',
    'alpha'                => ':attribute може містити лише літери.',
    'alpha_dash'           => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => ':attribute  може містити лише літери і цифри.',
    'array'                => ':attribute повинен бути масивом.',
    'before'               => ':attribute повинна бути дата, що раніша :date.',
    'before_or_equal'      => ':attribute повинна бути дата, що раніша або рівна :date.',
    'between'              => [
        'numeric' => ':attribute повинен бути в діпазоні :min <= :attribute <= :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => ':attribute повинен містити мінімум :min і максимум :max символів.',
        'array'   => ':attribute повинен містити мінімум :min і максимум :max елементів.',
    ],
    'boolean'              => ':attribute повинен бути true або false.',
    'confirmed'            => 'Паролі не збігаються.',
    'date'                 => ':attribute не валідна дата.',
    'date_format'          => ':attribute не відповідає формату :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => 'The :attribute must be a valid email address.',
    'exists'               => 'The selected :attribute is invalid.',
    'file'                 => ':attribute повинен бути файл.',
    'filled'               => 'The :attribute field must have a value.',
    'image'                => ':attribute повинен містити зображення.',
    'in'                   => 'вибраний :attribute невалідний.',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => 'поле :attribute повинно бути integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'ipv4'                 => 'The :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'The :attribute must be a valid IPv6 address.',
    'json'                 => ':attribute повинен бути валідний JSON.',
    'max'                  => [
        'numeric' => 'максимальне значення для :attribute = :max.',
        'file'    => 'максимальний розмір для :attribute = :max kb.',
        'string'  => ':attribute повинен містити не більше :max символів.',
        'array'   => ':attribute повинен містити не більше :max елементів.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'mimetypes'            => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'мінімальне значення для :attribute = :min.',
        'file'    => 'мінімальний розмір для :attribute = :min kb.',
        'string'  => ':attribute повинен містити не менше :min символів.',
        'array'   => ':attribute повинен містити не менше :min елементів.',
    ],
    'not_in'               => 'вибраний :attribute є не валідний.',
    'numeric'              => ':attribute повинен бути числом.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => ':attribute формат недійсний.',
    'required'             => ':attribute є обовязковим параметром.',
    'required_if'          => ':attribute є обовязковим коли :other є :value.',
    'required_unless'      => ':attribute є обовязковим якщо не :other є в :values.',
    'required_with'        => ':attribute є обовязковим коли :values є присутні.',
    'required_with_all'    => ':attribute є обовязковим коли :values є присутні.',
    'required_without'     => 'поле :attribute обовязкове якщо :values відсутні.',
    'required_without_all' => 'поле :attribute обовязкове, якщо немає жодного :values.',
    'same'                 => ':attribute і :other повинні відповідати.',
    'size'                 => [
        'numeric' => ':attribute повинен бути :size.',
        'file'    => ':attribute повинен бути :size kb.',
        'string'  => ':attribute повинен бути :size символів.',
        'array'   => ':attribute повинен містити :size елементів.',
    ],
    'string'               => ':attribute повинен бути рядок.',
    'timezone'             => ':attribute повинна бути валідна зона.',
    'unique'               => 'Даний :attribute вже використовується.',
    'uploaded'             => ':attribute не вдалося завантажити.',
    'url'                  => ':attribute формат не валідний.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
