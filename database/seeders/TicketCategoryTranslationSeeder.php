<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketCategoryTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $rnd = DB::table('ticket_categories')->where('slug', 'rnd')->value('id');
        $preparation = DB::table('ticket_categories')->where('slug', 'preparation')->value('id');
        $print = DB::table('ticket_categories')->where('slug', 'print')->value('id');

        DB::table('ticket_category_translations')->insert([
            // ── R&D ──────────────────────────────────────────────────────────
            [
                'ticket_category_id' => $rnd,
                'locale' => 'en-UK',
                'name' => 'R&D',
                'description' => "Full product development from scratch. You have an idea — we model, prepare and print it.\n\n".
                    "Please submit a fully detailed description of your project. Include photos, sketches, reference images, dimensions, materials, colours, and any other information relevant to the project. The more detail you provide, the more accurate our quote will be.\n\n".
                    "What to attach: photos / sketches / reference images / any existing files.\n\n".
                    "Our team will review your request, may ask for additional information, and will provide a full quote covering R&D, Preparation and Print, along with an estimated development and production timeline.\n\n".
                    'Pricing: calculated based on project complexity and total print time. The quote includes everything.',
            ],
            [
                'ticket_category_id' => $rnd,
                'locale' => 'pt-PT',
                'name' => 'I&D — Desenvolvimento',
                'description' => "Desenvolvimento completo do produto a partir de uma ideia. Você tem a ideia — nós modelamos, preparamos e imprimimos.\n\n".
                    "Envie uma descrição detalhada do seu projeto. Inclua fotos, esboços, imagens de referência, dimensões, materiais, cores e qualquer outra informação relevante. Quanto mais detalhe fornecer, mais preciso será o orçamento.\n\n".
                    "O que anexar: fotos / esboços / imagens de referência / ficheiros existentes.\n\n".
                    "A nossa equipa analisará o pedido, poderá solicitar informações adicionais e apresentará um orçamento completo que inclui I&D, Preparação e Impressão, com estimativa de tempo de desenvolvimento e produção.\n\n".
                    'Preço: calculado com base na complexidade do projeto e no tempo total de impressão. O orçamento inclui tudo.',
            ],

            // ── PREPARATION ──────────────────────────────────────────────────
            [
                'ticket_category_id' => $preparation,
                'locale' => 'en-UK',
                'name' => 'Preparation',
                'description' => "You already have a 3D model file, but it needs to be optimised for printing.\n\n".
                    "This service is ideal for architects, designers or students who have a project model that is not yet print-ready — it may need simplification, repair, scaling or material optimisation. It is also available if you want to customise a product we already sell in our store for a specific need.\n\n".
                    "What to attach: your 3D file(s) plus full technical specifications — scale, colours, materials, tolerances, and any other relevant details.\n\n".
                    "Our team will provide a separate quote for Preparation and for Print, along with estimated times for each.\n\n".
                    "Pricing:\n".
                    "  • Preparation — €30 / hour, billed in 15-minute increments.\n".
                    "  • Print — €20 / hour, billed in 15-minute increments (PLA included; other materials quoted separately).\n".
                    '  • 20% student discount applies to both Preparation and Print.',
            ],
            [
                'ticket_category_id' => $preparation,
                'locale' => 'pt-PT',
                'name' => 'Preparação',
                'description' => "Já tem um ficheiro com um modelo 3D, mas este precisa de ser otimizado para impressão.\n\n".
                    "Este serviço é ideal para arquitetos, designers ou estudantes que têm um modelo do seu projeto mas cujo ficheiro ainda não está pronto para imprimir — pode precisar de simplificação, reparação, escala ou otimização de material. Também está disponível se pretender personalizar um produto que já vendemos na nossa loja para uma necessidade específica.\n\n".
                    "O que anexar: o(s) ficheiro(s) 3D e as especificações técnicas completas — escala, cores, materiais, tolerâncias e quaisquer outros detalhes relevantes.\n\n".
                    "A nossa equipa apresentará um orçamento separado para Preparação e para Impressão, com estimativas de tempo para cada fase.\n\n".
                    "Preços:\n".
                    "  • Preparação — 30€ / hora, faturado em incrementos de 15 minutos.\n".
                    "  • Impressão — 20€ / hora, faturado em incrementos de 15 minutos (PLA incluído; outros materiais orçamentados separadamente).\n".
                    '  • Desconto de 20% para estudantes em Preparação e Impressão.',
            ],

            // ── PRINT ────────────────────────────────────────────────────────
            [
                'ticket_category_id' => $print,
                'locale' => 'en-UK',
                'name' => 'Print',
                'description' => "You have a fully print-ready file and just need it printed.\n\n".
                    "This is ideal for advanced users, students, or anyone who has already prepared their own file or found a model online ready to print.\n\n".
                    "What to attach: your print-ready file(s) plus all relevant technical information — scale, colour, material, layer height, infill percentage, and any other print settings.\n\n".
                    "Our team will review your request and reply with a quote and estimated production time.\n\n".
                    "Pricing:\n".
                    "  • €20 / hour, billed in 15-minute increments.\n".
                    "  • PLA material included. Other materials are quoted on a case-by-case basis.\n".
                    '  • 20% student discount applies.',
            ],
            [
                'ticket_category_id' => $print,
                'locale' => 'pt-PT',
                'name' => 'Impressão',
                'description' => "Já tem um ficheiro totalmente preparado para impressão e apenas pretende imprimir.\n\n".
                    "Ideal para utilizadores experientes, estudantes ou qualquer pessoa que já tenha preparado o seu ficheiro ou encontrado um modelo online pronto a imprimir.\n\n".
                    "O que anexar: o(s) ficheiro(s) prontos para impressão e todas as informações técnicas relevantes — escala, cor, material, altura de camada, percentagem de preenchimento e outras definições de impressão.\n\n".
                    "A nossa equipa analisará o pedido e responderá com um orçamento e estimativa de tempo de produção.\n\n".
                    "Preços:\n".
                    "  • 20€ / hora, faturado em incrementos de 15 minutos.\n".
                    "  • Material PLA incluído. Outros materiais são orçamentados caso a caso.\n".
                    '  • Desconto de 20% para estudantes.',
            ],
        ]);
    }
}
