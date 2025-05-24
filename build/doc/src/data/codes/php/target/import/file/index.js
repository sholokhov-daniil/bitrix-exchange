import {crossMark, isRight} from "@/utils/emojis";

export const exampleDataFormat = `
// ${isRight} Верно
$source = [
    [
        'path' => '....',
    ],
    [
        'path' => '....'
    ]
];

// ${crossMark} Неверно
$source = [
    [
        'path' => [
            '...',
            '...',
        ]
    ],
    [
        'path' => [
            '...',
            '...',
        ]
    ]
];
`;