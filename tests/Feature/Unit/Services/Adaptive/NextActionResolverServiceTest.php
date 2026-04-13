<?php

namespace Tests\Feature\Unit\Services\Adaptive;

use App\Models\Material;
use App\Models\Question;
use App\Services\Adaptive\NextActionResolverService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NextActionResolverServiceTest extends TestCase
{
    #[DataProvider('questionActionProvider')]
    public function test_question_actions_always_resolve_to_base_questions_route(string $actionCommand): void
    {
        $resolver = new NextActionResolverService;

        $material        = new Material;
        $material->id    = 'material-001';
        $material->title = 'Material OOP';

        $question                  = new Question;
        $question->id              = 'question-001';
        $question->sub_material_id = 'sub-material-001';

        $result = $resolver->resolve($actionCommand, $material, $question);

        $this->assertSame('question', $result['type']);
        $this->assertSame(
            route('mahasiswa.materials.questions.show', ['material' => $material->id]),
            $result['url'],
        );
    }

    /** @return array<string, array{0: string}> */
    public static function questionActionProvider(): array
    {
        return [
            'reduce difficulty'     => ['REDUCE_DIFFICULTY'],
            'increase difficulty'   => ['INCREASE_DIFFICULTY'],
            'default next question' => ['NEXT_QUESTION'],
        ];
    }
}
