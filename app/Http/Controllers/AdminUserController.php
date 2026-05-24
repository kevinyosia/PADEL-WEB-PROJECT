<?php

namespace App\Http\Controllers;

use App\Http\Requests\BanUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display paginated list of customer users with search.
     */
    public function index(Request $request): View
    {
        $query = User::query()->customers();

        // Search by name, email, or phone
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Stats for dashboard cards
        $stats = [
            'total' => User::query()->customers()->count(),
            'active' => User::query()->customers()->whereNull('banned_at')->count(),
            'banned' => User::query()->customers()->whereNotNull('banned_at')->count(),
            'member' => User::query()->customers()->whereHas('membership')->count(),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'stats' => $stats,
            'search' => $search,
        ]);
    }

    /**
     * Display user detail with transaction history.
     */
    public function show(User $user): View
    {
        $user->load([
            'reservations.court',
            'reservations.coach',
            'reservations.transaction',
            'reservations.equipment',
            'membership',
            'membershipPayments',
            'pointHistories',
        ]);

        // Calculate transaction summaries
        $transactionSummary = [
            'total_reservations' => $user->reservations->count(),
            'total_spent' => $user->reservations
                ->pluck('transaction')
                ->filter()
                ->where('status_pembayaran', 'lunas')
                ->sum('grand_total'),
            'total_court_spending' => $user->reservations
                ->pluck('transaction')
                ->filter()
                ->where('status_pembayaran', 'lunas')
                ->sum('total_harga_lapangan'),
            'total_coach_spending' => $user->reservations
                ->pluck('transaction')
                ->filter()
                ->where('status_pembayaran', 'lunas')
                ->sum('total_harga_coach'),
            'total_equipment_spending' => $user->reservations
                ->pluck('transaction')
                ->filter()
                ->where('status_pembayaran', 'lunas')
                ->sum('total_harga_perlengkapan'),
            'total_membership_spending' => $user->membershipPayments
                ->where('status', 'paid')
                ->sum('amount'),
        ];

        return view('admin.users.show', [
            'user' => $user,
            'transactionSummary' => $transactionSummary,
        ]);
    }

    /**
     * Ban a user account.
     */
    public function ban(BanUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $user->update([
            'banned_at' => now(),
            'banned_reason' => $validated['reason'],
        ]);

        return redirect()->back()->with('success', "User {$user->name} berhasil diblokir.");
    }

    /**
     * Unban a user account.
     */
    public function unban(User $user): RedirectResponse
    {
        $user->update([
            'banned_at' => null,
            'banned_reason' => null,
        ]);

        return redirect()->back()->with('success', "User {$user->name} berhasil di-unban.");
    }

    /**
     * Anonymize and soft-delete a user (UU PDP compliance).
     * Scrubs PII while preserving financial/transactional data integrity.
     */
    public function anonymize(User $user): RedirectResponse
    {
        // Prevent anonymizing admin/manajemen accounts
        if (in_array($user->role, ['admin', 'manajemen'])) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun admin atau manajemen.');
        }

        $userName = $user->name;

        // Scrub PII data
        $user->update([
            'name' => 'Deleted User #'.$user->id,
            'email' => 'deleted_'.md5($user->email).'@anonymized.local',
            'phone' => null,
            'photo' => null,
            'password' => Hash::make(Str::random(32)),
            'banned_at' => null,
            'banned_reason' => null,
        ]);

        // Soft delete
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Data user {$userName} berhasil dianonimkan dan dihapus.");
    }
}
