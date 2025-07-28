<?php

namespace Tests\Unit\Services\FeedbackAllocation;

use App\Exceptions\FeedbackAllocationException;
use App\Services\FeedbackAllocation\DefaultFeedbackAllocator;
use App\Services\FeedbackAllocation\FeedbackAllocator;
use PHPUnit\Framework\TestCase;

class FeedbackAllocatorTest extends TestCase
{
    private FeedbackAllocator $allocator;

    public function test_BasicFeedbackAllocation()
    {
        $trainerCapacities = [['Alice', 2], ['Bob', 1]];
        $participantWishes = [
            ['John', 'Alice', 'Bob'], // John prefers Alice first, then Bob
            ['Jane', 'Bob', 'Alice']  // Jane prefers Bob first, then Alice
        ];
        $numberOfWishes = 2;
        $forbiddenWishes = [['Jane', 'Alice']]; // Jane cannot be assigned to Alice
        $defaultPriority = 10; // High default priority signifies low preference

        $result = $this->allocator->tryToAllocateFeedbacks(
            $trainerCapacities,
            $participantWishes,
            $numberOfWishes,
            $forbiddenWishes,
            $defaultPriority
        );

        $expected = [
            [
                'trainerIdent' => 'Alice',
                'participantIdents' => ['John']
            ],
            [
                'trainerIdent' => 'Bob',
                'participantIdents' => ['Jane']
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_noTrainerAssignmentPossible()
    {
        $trainerCapacities = [['Alice', 0]]; // Alice cannot handle any participant
        $participantWishes = [
            ['John', 'Alice'], // John wants Alice
            ['Jane', 'Alice']  // Jane also wants Alice
        ];
        $numberOfWishes = 1;
        $forbiddenWishes = [];
        $defaultPriority = 10;

        $this->expectException(FeedbackAllocationException::class);
        $this->expectExceptionMessage('t.views.admin.feedbacks.allocation.errors.allocation_failed');

        $this->allocator->tryToAllocateFeedbacks(
            $trainerCapacities,
            $participantWishes,
            $numberOfWishes,
            $forbiddenWishes,
            $defaultPriority
        );
    }

    public function test_noParticipantPreferences()
    {
        $trainerCapacities = [['Alice', 1], ['Bob', 1]];
        $participantWishes = [
            ['John', null, null], // John has no preferences
            ['Jane', 'Bob', null]  // Jane has no preferences
        ];
        $numberOfWishes = 2;
        $forbiddenWishes = [];
        $defaultPriority = 10;

        $result = $this->allocator->tryToAllocateFeedbacks(
            $trainerCapacities,
            $participantWishes,
            $numberOfWishes,
            $forbiddenWishes,
            $defaultPriority
        );

        $expected = [
            [
                'trainerIdent' => 'Alice',
                'participantIdents' => ['John']
            ],
            [
                'trainerIdent' => 'Bob',
                'participantIdents' => ['Jane']
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_noFeedbackAssignmentPossible()
    {
        $trainerCapacities = [['Alice', 1]]; // Alice can only handle one participant
        $participantWishes = [
            ['John', 'Alice'], // John wants Alice
            ['Jane', 'Alice']  // Jane also wants Alice
        ];
        $numberOfWishes = 1;
        $forbiddenWishes = [];
        $defaultPriority = 10;

        $this->expectException(FeedbackAllocationException::class);
        $this->expectExceptionMessage('t.views.admin.feedbacks.allocation.errors.allocation_failed');

        $this->allocator->tryToAllocateFeedbacks(
            $trainerCapacities,
            $participantWishes,
            $numberOfWishes,
            $forbiddenWishes,
            $defaultPriority
        );
    }

    public function test_noPreferencesFeedbackAllocationConsiderForbiddenWishes()
    {
        //given
        $trainerCapacities = [['Chips', 3], ['Salz', 3], ['Paprika', 3], ['Käse', 3], ['Salzstange', 3]];
        $participantWishes = [
            ['Haribo'],
            ['Schoggi'],
            ['Fanta'],
            ['Coke'],
            ['Gummibär'],
            ['Zucker'],
            ['Sugus'],
            ['Ricola'],
            ['Rivella'],
            ['Sweet'],
            ['Ovi'],
            ['Honig'],
            ['Vanille'],
            ['Citro']
        ];
        $numberOfWishes = 0;
        $forbiddenWishes = [
            ['Sugus', 'Chips'],
            ['Sweet', 'Chips'],
            ['Ovi', 'Chips'],
            ['Honig', 'Chips'],
            ['Coke', 'Salz'],
            ['Sugus', 'Salz'],
            ['Ovi', 'Salz'],
            ['Zucker', 'Salz'],
            ['Vanille', 'Paprika'],
            ['Citro', 'Paprika'],
            ['Gummibär', 'Paprika'],
            ['Sugus', 'Paprika'],
            ['Fanta', 'Salzstange'],
            ['Honig', 'Salzstange'],
            ['Rivella', 'Salzstange'],
            ['Sugus', 'Salzstange']
        ];
        $defaultPriority = 10;

        //when
        $result = $this->allocator->tryToAllocateFeedbacks(
            $trainerCapacities,
            $participantWishes,
            $numberOfWishes,
            $forbiddenWishes,
            $defaultPriority
        );

        //then
        $this->assertNoForbiddenAllocation($result, $forbiddenWishes);
    }

    public function test_realCourseScenarioFeedbackAllocation()
    {
        //given
        $trainerCapacities = [
            ['Bubblegum', 4],
            ['Gingersnap', 3],
            ['Champ', 4],
            ['Ash', 4],
            ['Pecan', 3],
            ['Bello', 4],
            ['Skunk', 3]
        ];
        $participantWishes = [
            ['Gumdrop', 'Pecan', 'Bello', 'Champ'],
            ['Duckling', 'Pecan', 'Bubblegum', 'Champ'],
            ['Sams', 'Champ', 'Gingersnap', 'Pecan'],
            ['Frankfurter', 'Bello', 'Pecan', 'Skunk'],
            ['Red', 'Bubblegum', 'Skunk', 'Champ'],
            ['Heisenberg', 'Gingersnap', 'Bubblegum', 'Bello'],
            ['Guy', 'Champ', 'Bello', 'Bubblegum'],
            ['Chickie', 'Ash', 'Skunk', 'Champ'],
            ['Twiggy', 'Gingersnap', 'Champ', null],
            ['Kirby', 'Ash', 'Bello', 'Champ'],
            ['Doc', 'Ash', 'Bubblegum', 'Skunk'],
            ['Scratchy', 'Skunk', 'Bubblegum', 'Champ'],
            ['Turkey', 'Pecan', 'Champ', 'Skunk'],
            ['Dawg', 'Champ', 'Bello', 'Skunk'],
            ['Chance', 'Champ', 'Bello', null],
            ['Pearl', 'Gingersnap', 'Pecan', 'Bello'],
            ['Chamuya', 'Pecan', 'Bello', 'Champ'],
            ['Vale', 'Gingersnap', 'Bubblegum', 'Champ'],
            ['Pixel', 'Skunk', 'Champ', 'Bello'],
            ['Goose', null, null, null],
            ['Belch', null, null, null],
            ['Toodler', 'Skunk', 'Bello', 'Champ'],
            ['Taco', 'Champ', 'Bello', 'Gingersnap']
        ];
        $numberOfWishes = 3;
        $forbiddenWishes = [
            ['Frankfurter', 'Pecan'],
            ['Guy', 'Pecan'],
            ['Gumdrop', 'Pecan'],
            ['Doc', 'Pecan'],
            ['Heisenberg', 'Pecan'],
            ['Chance', 'Pecan'],
            ['Goose', 'Pecan'],
            ['Guy', 'Skunk'],
            ['Doc', 'Skunk'],
            ['Heisenberg', 'Skunk'],
            ['Chance', 'Skunk'],
            ['Goose', 'Skunk'],
            ['Guy', 'Gingersnap'],
            ['Doc', 'Gingersnap'],
            ['Heisenberg', 'Gingersnap'],
            ['Chance', 'Gingersnap'],
            ['Goose', 'Gingersnap'],
            ['Guy', 'Ash'],
            ['Pixel', 'Gingersnap'],
            ['Gumdrop', 'Gingersnap']
        ];
        $defaultPriority = 10;

        //when
        $result = $this->allocator->tryToAllocateFeedbacks(
            $trainerCapacities,
            $participantWishes,
            $numberOfWishes,
            $forbiddenWishes,
            $defaultPriority
        );

        //then
        $expected = [
            ['trainerIdent' => 'Bubblegum', 'participantIdents' => ['Red', 'Heisenberg', 'Goose', 'Belch']],
            ['trainerIdent' => 'Gingersnap', 'participantIdents' => ['Twiggy', 'Pearl', 'Vale']],
            ['trainerIdent' => 'Champ', 'participantIdents' => ['Sams', 'Guy', 'Dawg', 'Chance']],
            ['trainerIdent' => 'Ash', 'participantIdents' => ['Chickie', 'Kirby', 'Doc']],
            ['trainerIdent' => 'Pecan', 'participantIdents' => ['Duckling', 'Turkey', 'Chamuya']],
            ['trainerIdent' => 'Bello', 'participantIdents' => ['Gumdrop', 'Frankfurter', 'Taco']],
            ['trainerIdent' => 'Skunk', 'participantIdents' => ['Scratchy', 'Pixel', 'Toodler']]
        ];
        sort($expected);
        sort($result);

        $this->assertEquals($expected, $result);
        $this->assertNoForbiddenAllocation($result, $forbiddenWishes);
    }

    public function test_shouldThrowExceptionBecauseOfTooManyForbiddenWishesAndThereforeToLowCapacity()
    {
        //given
        $trainerCapacities = [['Chips', 4], ['Salz', 3]];
        $participantWishes =
            [
                ['Haribo', 'Chips', 'Salz'],
                ['Schoggi', 'Chips', 'Salz'],
                ['Fanta', 'Chips', 'Salz'],
                ['Coke', 'Chips', 'Salz'],
                ['Gummibär', 'Salz', 'Chips'],
                ['Zucker', 'Salz', 'Chips']
            ];
        $numberOfWishes = 2;
        $forbiddenWishes =
            [
                ['Haribo', 'Chips'],
                ['Schoggi', 'Chips'],
                ['Fanta', 'Chips'],
                ['Gummibär', 'Chips'],
                ['Coke', 'Salz'],
                ['Zucker', 'Salz']
            ];
        $defaultPriority = 6;

        // when & then
        $this->expectException(FeedbackAllocationException::class);
        $this->expectExceptionMessage('t.views.admin.feedbacks.allocation.errors.allocation_failed');

        $this->allocator->tryToAllocateFeedbacks(
            $trainerCapacities,
            $participantWishes,
            $numberOfWishes,
            $forbiddenWishes,
            $defaultPriority
        );
    }

    /**
     * @dataProvider allocationDataProvider
     */
    public function test_FeedbackAllocationWithSampleInput($trainerCapacities, $participantWishes, $numberOfWishes, $forbiddenWishes, $defaultPriority, $expected)
    {
        // Given

        // When
        $result = $this->allocator->tryToAllocateFeedbacks($trainerCapacities, $participantWishes, $numberOfWishes, $forbiddenWishes, $defaultPriority);

        // Then
        sort($expected);
        sort($result);

        $this->assertEquals($expected, $result);
        $this->assertNoForbiddenAllocation($result, $forbiddenWishes);

    }

    public static function allocationDataProvider(): array

    {

        $defaultPriority = 10;
        return [
            // Test Case 0
            [
                [['Salz', 3], ['Paprika', 2], ['Chips', 1]], // Trainer Capacities
                [
                    ['Nutella', 'Chips', 'Paprika'],
                    ['Honig', 'Paprika', 'Salz'],
                    ['Coke', 'Salz', 'Chips']
                ], // Participant Wishes
                2, // Number of Wishes
                [], // Forbidden Wishes
                $defaultPriority, // Default Priority
                [
                    [
                        'trainerIdent' => 'Salz',
                        'participantIdents' => ['Coke']
                    ],
                    [
                        'trainerIdent' => 'Paprika',
                        'participantIdents' => ['Honig']
                    ],
                    [
                        'trainerIdent' => 'Chips',
                        'participantIdents' => ['Nutella']
                    ]
                ] // Expected Result
            ],
            // 1. richtiger
            [
                [['Salz', 3], ['Paprika', 2], ['Chips', 1]],
                [
                    ['Nutella', 'Chips', 'Paprika'],
                    ['Honig', 'Paprika', 'Salz'],
                    ['Coke', 'Salz', 'Chips'],
                    ['Schoggi', 'Salz', 'Paprika'],
                    ['Haribo', 'Paprika', 'Salz']
                ],
                2,
                [['Nutella', 'Chips']],
                $defaultPriority,
                [
                    [
                        'trainerIdent' => 'Salz',
                        'participantIdents' => ['Honig', 'Coke', 'Schoggi']
                    ],
                    [
                        'trainerIdent' => 'Paprika',
                        'participantIdents' => ['Nutella', 'Haribo']
                    ] /*,
                    [
                        'trainerIdent' => 'Chips',
                        'participantIdents' => []
                    ] */
                ]

            ],
            [ // 2. richtiger
                [['Chips', 1], ['Salz', 1], ['Paprika', 1], ['Käse', 1]],
                [
                    ['Haribo', 'Chips', 'Salz', 'Paprika'],
                    ['Schoggi', 'Chips', 'Paprika', 'Salz'],
                    ['Fanta', 'Paprika', 'Salz', 'Chips'],
                    ['Coke', 'Salz', 'Chips', 'Paprika']
                ],
                3,
                [],
                $defaultPriority,
                [
                    [
                        'trainerIdent' => 'Chips',
                        'participantIdents' => ['Haribo']
                    ],
                    [
                        'trainerIdent' => 'Salz',
                        'participantIdents' => ['Coke']
                    ],
                    [
                        'trainerIdent' => 'Paprika',
                        'participantIdents' => ['Fanta']
                    ],
                    [
                        'trainerIdent' => 'Käse',
                        'participantIdents' => ['Schoggi']
                    ]
                ]

            ],
            [ // 3.
                [['Chips', 1], ['Salz', 1], ['Paprika', 1], ['Käse', 1]],
                [
                    ['Rivella', 'Chips', 'Salz', 'Paprika'],
                    ['Honig', 'Salz', 'Käse', 'Paprika'],
                    ['Schoggi', 'Käse', 'Chips', 'Salz'],
                    ['Haribo', 'Salz', 'Paprika', 'Käse']
                ],
                3,
                [],
                $defaultPriority,
                [
                    [
                        'trainerIdent' => 'Chips',
                        'participantIdents' => ['Rivella']
                    ],
                    [
                        'trainerIdent' => 'Salz',
                        'participantIdents' => ['Honig']
                    ],
                    [
                        'trainerIdent' => 'Paprika',
                        'participantIdents' => ['Haribo']
                    ],
                    [
                        'trainerIdent' => 'Käse',
                        'participantIdents' => ['Schoggi']
                    ]
                ]

            ],
            [ // 4.
                [['Chips', 3], ['Salz', 3], ['Paprika', 1], ['Käse', 1]],
                [
                    ['Rivella', 'Salz', 'Paprika', 'Käse'],
                    ['Coke', 'Salz', 'Paprika', 'Käse'],
                    ['Schoggi', 'Käse', 'Paprika', 'Salz'],
                    ['Haribo', 'Chips', 'Salz', 'Käse'],
                    ['Honig', 'Chips', 'Paprika', 'Käse'],
                    ['Nutella', 'Paprika', 'Salz', 'Käse'],
                    ['Glace', 'Salz', 'Chips', 'Paprika'],
                    ['Fanta', 'Chips', 'Salz', 'Käse']
                ],
                3,
                [['Haribo', 'Chips']],
                $defaultPriority,
                [
                    [
                        'trainerIdent' => 'Chips',
                        'participantIdents' => ['Honig', 'Glace', 'Fanta']
                    ],
                    [
                        'trainerIdent' => 'Salz',
                        'participantIdents' => ['Rivella', 'Coke', 'Haribo']
                    ],
                    [
                        'trainerIdent' => 'Paprika',
                        'participantIdents' => ['Nutella']
                    ],
                    [
                        'trainerIdent' => 'Käse',
                        'participantIdents' => ['Schoggi']
                    ]
                ]
            ],
            [ // 5.
                [['Chips', 3], ['Salz', 3], ['Paprika', 3], ['Käse', 3], ['Salzstange', 3]],
                [
                    ['Haribo', 'Chips', 'Paprika', 'Salzstange'],
                    ['Schoggi', 'Chips', 'Paprika', 'Käse'],
                    ['Fanta', 'Chips', 'Salzstange', 'Chips'],
                    ['Coke', 'Salz', 'Käse', 'Salzstange'],
                    ['Gummibär', 'Salz', 'Salzstange', 'Salz'],
                    ['Zucker', 'Salz', 'Chips', 'Paprika'],
                    ['Sugus', 'Paprika', 'Salz', 'Käse'],
                    ['Ricola', 'Käse', 'Paprika', 'Salzstange'],
                    ['Rivella', 'Salzstange', 'Chips', null],
                    ['Ovi', 'Salzstange', 'Chips', 'Salz'],
                    ['Sweet', 'Käse', 'Salzstange', 'Chips']
                ],
                3,
                [
                    ['Rivella', 'Salzstange'],
                    ['Ovi', 'Salzstange'],
                    ['Sweet', 'Salzstange'],
                    ['Gummibär', 'Salz'],
                    ['Zucker', 'Salz'],
                    ['Fanta', 'Chips'],
                    ['Sweet', 'Chips']
                ],
                $defaultPriority,
                [
                    [
                        'trainerIdent' => 'Chips',
                        'participantIdents' => ['Schoggi', 'Zucker', 'Rivella']
                    ],
                    [
                        'trainerIdent' => 'Salz',
                        'participantIdents' => ['Coke', 'Ovi']
                    ],
                    [
                        'trainerIdent' => 'Paprika',
                        'participantIdents' => ['Haribo', 'Sugus']
                    ],
                    [
                        'trainerIdent' => 'Käse',
                        'participantIdents' => ['Ricola', 'Sweet']
                    ],
                    [
                        'trainerIdent' => 'Salzstange',
                        'participantIdents' => ['Fanta', 'Gummibär']
                    ]
                ]
            ],
            [ # 6. Only two of three leaders are in preferences
                // Trainers and their capacities
                [['Chips', 4], ['Salz', 3], ['Paprika', 2]],
                // Participant wishes
                [
                    ['Haribo', 'Chips', 'Salz'],
                    ['Schoggi', 'Chips', 'Paprika'],
                    ['Fanta', 'Chips', 'Salz'],
                    ['Coke', 'Chips', 'Paprika'],
                    ['Gummibär', 'Salz', 'Chips'],
                    ['Zucker', 'Salz', 'Chips'],
                    ['Sugus', 'Chips', 'Salz'],
                    ['Ricola', 'Salz', 'Chips'],
                    ['Rivella', 'Salz', 'Chips']
                ],
                2, // Number of Wishes
                [['Haribo', 'Paprika'],
                    ['Zucker', 'Paprika'],
                    ['Coke', 'Paprika'],
                    ['Sugus', 'Paprika']
                ], // Forbidden Wishes
                $defaultPriority, // Default Priority
                [
                    [
                        'trainerIdent' => 'Chips',
                        'participantIdents' => ['Haribo', 'Fanta', 'Coke', 'Sugus']
                    ],
                    [
                        'trainerIdent' => 'Salz',
                        'participantIdents' => ['Gummibär', 'Zucker', 'Ricola']
                    ],
                    [
                        'trainerIdent' => 'Paprika',
                        'participantIdents' => ['Schoggi', 'Rivella']
                    ],
                ] // Expected Results
            ],
            [
                [['Chips', 3], ['Salz', 3], ['Paprika', 3], ['Käse', 2], ['Salzstange', 2]],
                [
                    ['Haribo', 'Chips', 'Salz', 'Paprika'],
                    ['Schoggi', 'Paprika', 'Salz', 'Käse'],
                    ['Fanta', 'Salz', 'Salzstange', 'Käse'],
                    ['Coke', 'Salzstange', 'Salz', 'Paprika'],
                    ['Gummibär', 'Paprika', 'Salzstange', 'Salz'],
                    ['Zucker', 'Salz', 'Käse', 'Salzstange'],
                    ['Sugus', 'Käse', 'Salz', 'Paprika'],
                    ['Ricola', 'Salz', 'Chips', 'Salzstange'],
                    ['Rivella', 'Salz', 'Chips', 'Salzstange'],
                    ['Sweet', 'Salz', 'Käse', 'Chips'],
                    ['Ovi', 'Käse', 'Salz', 'Paprika'],
                    ['Honig', 'Salz', 'Paprika', 'Salzstange']
                ],
                3,
                [
                    ['Rivella', 'Salz'],
                    ['Sweet', 'Salz'],
                    ['Ricola', 'Salz'],
                    ['Sugus', 'Salz'],
                    ['Zucker', 'Chips'],
                    ['Haribo', 'Chips']
                ],
                $defaultPriority,
                [
                    [
                        'trainerIdent' => 'Chips',
                        'participantIdents' => ['Ricola', 'Rivella', 'Sweet']
                    ],
                    [
                        'trainerIdent' => 'Salz',
                        'participantIdents' => ['Fanta', 'Zucker', 'Honig']
                    ],
                    [
                        'trainerIdent' => 'Paprika',
                        'participantIdents' => ['Haribo', 'Schoggi', 'Gummibär']
                    ],
                    [
                        'trainerIdent' => 'Käse',
                        'participantIdents' => ['Sugus', 'Ovi']
                    ],
                    [
                        'trainerIdent' => 'Salzstange',
                        'participantIdents' => ['Coke']
                    ]
                ]
            ],
            [
                [['Chips', 4], ['Salz', 3], ['Paprika', 2], ['Käse', 4], ['Salzstange', 2], ['Pfeffer', 1], ['Zwiback', 3], ['Chili', 5], ['Bouillon', 2], ['Senf', 3]],
                [
                    ['Haribo', 'Salz', 'Paprika', 'Käse', 'Senf'],
                    ['Schoggi', 'Salzstange', 'Käse', 'Zwiback', 'Chili'],
                    ['Fanta', 'Salz', 'Käse', 'Bouillon', 'Pfeffer'],
                    ['Coke', 'Käse', null, null, null],
                    ['Gummibär', 'Senf', 'Paprika', 'Chili', 'Käse']
                ],
                4,
                [
                    ['Coke', 'Käse'],
                    ['Fanta', 'Käse'],
                    ['Haribo', 'Käse'],
                    ['Schoggi', 'Käse'],
                    ['Fanta', 'Pfeffer'],
                    ['Fanta', 'Salzstange'],
                    ['Coke', 'Salzstange'],
                    ['Schoggi', 'Senf'],
                    ['Fanta', 'Senf'],
                    ['Coke', 'Zwiback'],
                    ['Coke', 'Chili'],
                    ['Coke', 'Bouillon'],
                    ['Coke', 'Paprika']
                ],
                $defaultPriority,
                [
                    [
                        'trainerIdent' => 'Chips',
                        'participantIdents' => ['Coke']
                    ],
                    [
                        'trainerIdent' => 'Salz',
                        'participantIdents' => ['Haribo', 'Fanta']
                    ],
                    [
                        'trainerIdent' => 'Salzstange',
                        'participantIdents' => ['Schoggi']
                    ],
                    [
                        'trainerIdent' => 'Senf',
                        'participantIdents' => ['Gummibär']
                    ],
                    /*
                    [
                        'trainerIdent' => 'Paprika',
                        'participantIdents' => []
                    ],
                    [
                        'trainerIdent' => 'Käse',
                        'participantIdents' => []
                    ],
                    [
                        'trainerIdent' => 'Pfeffer',
                        'participantIdents' => []
                    ],
                    [
                        'trainerIdent' => 'Zwiback',
                        'participantIdents' => []
                    ],
                    [
                        'trainerIdent' => 'Chili',
                        'participantIdents' => []
                    ],
                    [
                        'trainerIdent' => 'Bouillon',
                        'participantIdents' => []
                    ]
                    */
                ]
            ]
        ];
    }

    /**
     * @param array $result
     * @param array $forbiddenWishes
     * @return void
     */
    public function assertNoForbiddenAllocation(array $result, array $forbiddenWishes): void
    {
        foreach ($result as $trainer) {
            // Checking that no forbidden pairs are in the result
            foreach ($forbiddenWishes as $forbidden) {
                if ($trainer['trainerIdent'] === $forbidden[1]) { // Matching trainer name
                    $this->assertNotContains($forbidden[0], $trainer['participantIdents'],
                        "Forbidden pair $forbidden[0] and $forbidden[1] found in the results.");
                }
            }
        }
    }

    protected function setUp(): void
    {
        $this->allocator = new DefaultFeedbackAllocator();
    }


}
