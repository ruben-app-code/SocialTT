<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicPollVoteController extends Controller
{
    /**
     * Registra o actualiza el voto (usuario autenticado o invitado con guest_token UUID).
     * Responde JSON con recuentos por opción para actualizar la UI sin recargar.
     */
    public function vote(Request $request, Poll $poll): JsonResponse
    {
        $validated = $request->validate([
            'poll_option_id' => ['required', 'integer', 'exists:poll_options,id'],
            'guest_token' => ['nullable', 'uuid'],
        ]);

        if (! $poll->isOpen()) {
            return response()->json([
                'message' => __('Esta encuesta está cerrada o ya no acepta votos.'),
            ], 422);
        }

        $option = PollOption::query()
            ->where('id', $validated['poll_option_id'])
            ->where('poll_id', $poll->id)
            ->firstOrFail();

        if (Auth::check()) {
            $voterKey = 'user:'.Auth::id();
            $userId = Auth::id();
        } else {
            $token = $validated['guest_token'] ?? null;
            if ($token === null || $token === '') {
                return response()->json([
                    'message' => __('Para votar como invitado se requiere un identificador (guest_token).'),
                ], 422);
            }
            $voterKey = 'guest:'.$token;
            $userId = null;
        }

        PollVote::query()->updateOrCreate(
            [
                'poll_id' => $poll->id,
                'voter_key' => $voterKey,
            ],
            [
                'option_id' => $option->id,
                'user_id' => $userId,
            ]
        );

        return response()->json($this->buildResultsPayload($poll));
    }

    /**
     * @return array{success: true, poll_id: int, total_votes: int, options: array<int, int>, authenticated: bool}
     */
    private function buildResultsPayload(Poll $poll): array
    {
        $poll->load(['pollOptions' => fn ($q) => $q->orderBy('id')->withCount('votes')]);

        $options = [];
        $total = 0;
        foreach ($poll->pollOptions as $opt) {
            $c = (int) $opt->votes_count;
            $options[$opt->id] = $c;
            $total += $c;
        }

        return [
            'success' => true,
            'poll_id' => $poll->id,
            'total_votes' => $total,
            'options' => $options,
            'authenticated' => Auth::check(),
        ];
    }
}
