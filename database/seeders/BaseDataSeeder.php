<?php

namespace Database\Seeders;

use App\Models\Country;
use Database\Seeders\Concerns\SeedsWithProgress;
use App\Models\CreatorLevel;
use App\Models\SocialNetwork;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BaseDataSeeder extends Seeder
{
    use SeedsWithProgress;

    public function run(): void
    {
        $this->seedSocialNetworks();
        $this->seedTopics();
        $this->seedCountries();
        $this->seedCreatorLevels();
    }

    private function seedSocialNetworks(): void
    {
        $networks = [
            ['name' => 'TikTok', 'slug' => 'tiktok'],
            ['name' => 'Instagram', 'slug' => 'instagram'],
            ['name' => 'YouTube', 'slug' => 'youtube'],
            ['name' => 'Twitter', 'slug' => 'twitter'],
            ['name' => 'X', 'slug' => 'x'],
            ['name' => 'Facebook', 'slug' => 'facebook'],
            ['name' => 'Twitch', 'slug' => 'twitch'],
            ['name' => 'LinkedIn', 'slug' => 'linkedin'],
            ['name' => 'Threads', 'slug' => 'threads'],
        ];

        foreach ($networks as $n) {
            SocialNetwork::firstOrCreate(['slug' => $n['slug']], $n);
            $this->seedDot();
        }
    }

    private function seedTopics(): void
    {
        $topics = [

            'Cristianismo' => [
                'Cristianismo Bíblico','Predicación Cristiana','Evangelismo','Apologética Cristiana',
                'Estudio Bíblico','Doctrina Cristiana','Teología Sistemática','Cristianismo Primitivo',
                'Vida Cristiana','Oración Cristiana','Ayuno Cristiano','Guerra Espiritual',
                'Liberación Espiritual','Testimonios Cristianos','Milagros Cristianos',
                'Profecía Bíblica','Escatología','Parábolas de Jesús','Enseñanzas de Cristo',
                'Discipulado','Santificación','Gracia y Fe','Pecado y Arrepentimiento',

                'Denominaciones' => [
                    'Catolicismo','Catolicismo Tradicional','Catolicismo Carismático',
                    'Protestantismo','Cristianismo Evangélico','Pentecostalismo',
                    'Bautistas','Metodistas','Luteranos','Adventistas',
                    'Testigos de Jehová','Mormones','Cristianismo Ortodoxo',
                    'Anglicanismo','Cristianismo Reformado','Calvinismo','Arminianismo',
                    'Cristianismo Gnóstico','Gnosis Cristiana',
                ],
            ],

            'Judaísmo' => [
                'Torá','Talmud','Kabbalah','Judaísmo Ortodoxo','Judaísmo Reformista',
            ],

            'Religiones' => [
                'Islam','Budismo','Hinduismo','Taoísmo','Confucianismo',
                'Espiritualidad Nueva Era','Meditación Espiritual','Reencarnación',
                'Chakras y Energía','Astrología','Ocultismo','Misticismo',
            ],

            'Debate y Filosofía' => [
                'Ateísmo','Agnosticismo','Debate Religioso','Crítica Bíblica',
                'Religión vs Ciencia','Filosofía Religiosa',
            ],

            'Negocios y Dinero' => [
                'Emprendimiento Digital','Negocios Online','Marketing Digital',
                'Ventas Online','Ventas de Alto Valor','Copywriting',
                'Marca Personal','Branding','Creación de Contenido',
                'Monetización en Redes','Ingresos Pasivos','Finanzas Personales',
                'Inversiones','Criptomonedas','Trading','Ecommerce',
                'Dropshipping','Amazon FBA','Negocios Locales',
                'Automatización de Negocios','SaaS','Startups',
                'Freelance','Trabajo Remoto','Productividad','Gestión del Tiempo',
            ],

            'Tecnología' => [
                'Programación Web' => [
                    'Laravel','PHP','JavaScript','Vue','React',
                    'Backend','Frontend','APIs','MySQL'
                ],
                'Infraestructura' => [
                    'DevOps','Cloud Computing','Ciberseguridad'
                ],
                'Inteligencia Artificial' => [
                    'Machine Learning','Automatización con IA','Bots'
                ],
                'Otros' => [
                    'Apps Móviles','Flutter','UI UX','Diseño Web'
                ],
            ],

            'Salud y Fitness' => [
                'Entrenamiento' => [
                    'Fuerza','Hipertrofia','Calistenia','Crossfit',
                    'Rutinas de Gym','Entrenamiento en Casa','Running'
                ],
                'Nutrición' => [
                    'Nutrición Deportiva','Dietas','Keto','Ayuno Intermitente'
                ],
                'Salud Mental' => [
                    'Ansiedad','Depresión','Hábitos Saludables','Biohacking'
                ],
                'Medicina' => [
                    'Medicina Natural'
                ],
            ],

            'Educación' => [
                'Matemáticas','Física','Química','Biología',
                'Historia Universal','Historia Bíblica','Geografía',
                'Idiomas','Aprender Inglés','Ciencia General',
            ],

            'Entretenimiento' => [
                'Comedia','Humor','Sketches','Standup',
                'Memes','Parodias','Cultura Pop',
                'Series','Películas','Anime','Reseñas de Cine',
            ],

            'Gaming' => [
                'Videojuegos','Gameplays','Streaming',
                'eSports','FPS','RPG','Retro Gaming',
            ],

            'Cocina' => [
                'Cocina Casera','Recetas','Comida Mexicana',
                'Comida Saludable','Cocina Keto','Postres',
                'Street Food','Parrilla',
            ],

            'Estilo de Vida' => [
                'Minimalismo','Vida en Pareja','Familia',
                'Paternidad','Motivación','Desarrollo Personal',
                'Hábitos','Rutinas Diarias',
            ],

            'Arte' => [
                'Dibujo','Pintura','Ilustración','Fotografía',
                'Edición de Video','Animación','Diseño Gráfico',
            ],

            'Música' => [
                'Rap','Hip Hop','Rock','Pop','Música Electrónica',
                'Producción Musical','Canto','Instrumentos Musicales',
            ],

            'Belleza y Moda' => [
                'Maquillaje','Cuidado de la Piel','Skincare',
                'Moda Urbana','Streetwear','Lujo','Barbería',
            ],

            'Viajes' => [
                'Viajes','Turismo','Backpacking','Aventura',
                'Viajes Económicos','Cultura Internacional',
            ],

            'Mascotas' => [
                'Perros','Gatos','Entrenamiento Canino',
                'Mascotas Exóticas',
            ],

            'Autos y Lujo' => [
                'Autos','Motos','Tuning','Mecánica',
                'Relojes','Coleccionismo','Lujo',
            ],

            'Redes Sociales' => [
                'TikTok','Instagram','YouTube',
                'Creadores de Contenido','Influencers',
            ],

            'Sociedad' => [
                'Noticias','Política','Opinión',
                'Debate Social','Criminología','Derecho',
            ],

            'Conciencia y Gnosis' => [
                'Conciencia','Despertar Espiritual',
                'Ego','Autoobservación','Recuerdo de Sí',
                'Trabajo Interior','Transformación Personal',
                'Samael Aun Weor','Gnosis','Cristo Interior',
            ],

        ];

        foreach ($topics as $mainName => $children) {
            $root = Topic::firstOrCreate(
                ['slug' => Str::slug($mainName)],
                ['name' => $mainName, 'parent_id' => null]
            );
            $this->seedDot();
            if (is_array($children)) {
                $this->seedTopicChildren($root, $children);
            }
        }
    }

    /**
     * Solo dos niveles: $parent es tema principal; aquí solo se crean subtemas.
     * Valores del array: strings (subtema) o clave string => lista de strings (grupo bajo el mismo padre).
     */
    private function seedTopicChildren(Topic $parent, array $items): void
    {
        foreach ($items as $key => $value) {
            if (is_string($value)) {
                $this->firstOrCreateChildTopic($parent, $value);
                continue;
            }
            if (! is_array($value) || ! is_string($key)) {
                continue;
            }
            foreach ($value as $sub) {
                if (is_string($sub)) {
                    $name = $key.' · '.$sub;
                    $slug = Str::slug($parent->slug.'-'.$key.'-'.$sub);
                    Topic::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $name, 'parent_id' => $parent->id]
                    );
                    $this->seedDot();
                }
            }
        }
    }

    private function firstOrCreateChildTopic(Topic $parent, string $name): void
    {
        $slug = Str::slug($parent->slug.'-'.$name);
        Topic::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'parent_id' => $parent->id]
        );
        $this->seedDot();
    }

    private function seedCountries(): void
    {
        $countries = [
            ['name' => 'México', 'code' => 'MX'],
            ['name' => 'España', 'code' => 'ES'],
            ['name' => 'Argentina', 'code' => 'AR'],
            ['name' => 'Colombia', 'code' => 'CO'],
            ['name' => 'Chile', 'code' => 'CL'],
            ['name' => 'Perú', 'code' => 'PE'],
            ['name' => 'Estados Unidos', 'code' => 'US'],
            ['name' => 'Venezuela', 'code' => 'VE'],
            ['name' => 'Ecuador', 'code' => 'EC'],
            ['name' => 'Guatemala', 'code' => 'GT'],
            ['name' => 'Cuba', 'code' => 'CU'],
            ['name' => 'Bolivia', 'code' => 'BO'],
            ['name' => 'República Dominicana', 'code' => 'DO'],
            ['name' => 'Honduras', 'code' => 'HN'],
            ['name' => 'Paraguay', 'code' => 'PY'],
            ['name' => 'El Salvador', 'code' => 'SV'],
            ['name' => 'Nicaragua', 'code' => 'NI'],
            ['name' => 'Costa Rica', 'code' => 'CR'],
            ['name' => 'Panamá', 'code' => 'PA'],
            ['name' => 'Uruguay', 'code' => 'UY'],
        ];

        foreach ($countries as $c) {
            Country::firstOrCreate(['code' => $c['code']], $c);
            $this->seedDot();
        }
    }

    private function seedCreatorLevels(): void
    {
        $levels = [
            ['name' => 'Principiante', 'min_followers' => 0, 'max_followers' => 1000, 'badge' => 'new'],
            ['name' => 'Emergente', 'min_followers' => 1001, 'max_followers' => 10000, 'badge' => 'rising'],
            ['name' => 'Crecimiento', 'min_followers' => 10001, 'max_followers' => 50000, 'badge' => 'growth'],
            ['name' => 'Establecido', 'min_followers' => 50001, 'max_followers' => 200000, 'badge' => 'established'],
            ['name' => 'Influencer', 'min_followers' => 200001, 'max_followers' => 1000000, 'badge' => 'influencer'],
            ['name' => 'Estrella', 'min_followers' => 1000001, 'max_followers' => 999999999, 'badge' => 'star'],
        ];

        foreach ($levels as $l) {
            CreatorLevel::firstOrCreate(['badge' => $l['badge']], $l);
            $this->seedDot();
        }
    }
}
