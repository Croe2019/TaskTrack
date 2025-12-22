<?php
namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class TeamInvitationController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $invites = TeamInvitation::with([
            'team',
            'inviter',
        ])
        ->where('invitee_id', Auth::id())
        ->where('status', TeamInvitation::STATUS_PENDING)
        ->get();

        return view('invites.index', compact('invites'));
    }

     // GET: 画面表示
    public function create(Team $team)
    {
        $this->authorize('createInvite', $team);

        $users = User::whereDoesntHave('teams', function ($q) use ($team) {
            $q->where('teams.id', $team->id);
        })->get();

        return view('teams.invites.create', compact('team', 'users'));
    }

   public function store(Request $request, Team $team)
    {
        $validated = $request->validate([
            'invitee_id' => 'required|exists:users,id',
            'role' => 'required|in:admin,member',
        ]);

        $this->authorize(
            'createInvite',
            [$team, $validated['role']]
        );

        // 二重招待防止
        if (TeamInvitation::where('team_id', $team->id)
            ->where('invitee_id', $validated['invitee_id'])
            ->where('status', TeamInvitation::STATUS_PENDING)
            ->exists()) {
            return back()->withErrors('すでに招待済みです');
        }

        TeamInvitation::create([
            'team_id'     => $team->id,
            'inviter_id'  => Auth::id(),
            'invitee_id'  => $validated['invitee_id'],
            'role'        => $validated['role'],
            'status'      => TeamInvitation::STATUS_PENDING,
        ]);

        return redirect()
            ->route('teams.show', $team)
            ->with('success', '招待を送信しました');
    }


    // 招待承認
    public function accept($inviteId)
    {
        $invite = TeamInvitation::where('id', $inviteId)
            ->where('invitee_id', Auth::id())
            ->firstOrFail();

        if ($invite->status !== TeamInvitation::STATUS_PENDING) {
            abort(409);
        }

        if ($invite->created_at->addDays(7)->isPast()) {
            abort(410);
        }

        DB::transaction(function () use ($invite) {

            $invite->team->members()->syncWithoutDetaching([
                $invite->invitee_id => [
                    'role' => $invite->role, // ← 招待時指定
                ],
            ]);

            $invite->update([
                'status' => TeamInvitation::STATUS_ACCEPTED,
            ]);
        });

        return redirect()
            ->route('teams.show', $invite->team)
            ->with('success', 'チームに参加しました');
    }

    // 招待拒否
    public function reject(TeamInvitation $invite)
    {
        abort_unless($invite->invitee_id === Auth::id(), 403);

        if ($invite->status !== TeamInvitation::STATUS_PENDING) {
            abort(409);
        }

        $invite->update([
            'status' => TeamInvitation::STATUS_REJECTED,
        ]);

        return redirect()->route('invites.index');
    }


}

