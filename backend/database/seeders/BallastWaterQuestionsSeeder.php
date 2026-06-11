<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class BallastWaterQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            [
                'question'   => 'Capacidade de Água de Lastro (em metros cúbicos)',
                'score'      => 0,
                'input_type' => 'number',
                'options'    => null,
            ],
            [
                'question'   => 'Método de Gestão da Água de Lastro utilizado',
                'score'      => 0,
                'input_type' => 'text',
                'options'    => null,
            ],
            [
                'question'   => 'Data de Instalação (se aplicável)',
                'score'      => 0,
                'input_type' => 'text',
                'options'    => null,
            ],
            [
                'question'   => 'Nome do Fabricante (se aplicável)',
                'score'      => 0,
                'input_type' => 'text',
                'options'    => null,
            ],
            [
                'question'   => 'O(s) método(s) principal(is) de Gestão da Água de Lastro utilizado(s) neste navio são',
                'score'      => 0,
                'input_type' => 'radio',
                'options'    => [
                    ['label' => 'de acordo com o regulamento D-1', 'value' => 'de acordo com o regulamento D-1', 'has_text_input' => false],
                    ['label' => 'de acordo com o regulamento D-2', 'value' => 'de acordo com o regulamento D-2', 'has_text_input' => false],
                    ['label' => '(descrever)',                     'value' => '(descrever)',                     'has_text_input' => true],
                    ['label' => 'o navio está sujeito ao regulamento D-4', 'value' => 'o navio está sujeito ao regulamento D-4', 'has_text_input' => false],
                ],
            ],
            [
                'question'   => 'O navio foi inspecionado de acordo com o regulamento E-1 do Anexo da Convenção',
                'score'      => 0,
                'input_type' => 'text',
                'options'    => null,
            ],
            [
                'question'   => 'A inspeção mostra que a Gestão da Água de Lastro no navio está em conformidade com o Anexo da Convenção',
                'score'      => 0,
                'input_type' => 'text',
                'options'    => null,
            ],
            [
                'question'   => 'Status da Inspeção',
                'score'      => 0,
                'input_type' => 'radio',
                'options'    => [
                    ['label' => 'Aprovado',  'value' => 'Aprovado',  'has_text_input' => false],
                    ['label' => 'Reprovado', 'value' => 'Reprovado', 'has_text_input' => false],
                ],
            ],
        ];

        foreach ($questions as $data) {
            Question::firstOrCreate(
                ['question' => $data['question']],
                [
                    'score'      => $data['score'],
                    'input_type' => $data['input_type'],
                    'options'    => $data['options'],
                ]
            );
        }
    }
}
