<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\NotificationController;
use App\Traits\HistorisableActions;

class AnnouncementController extends Controller
{
    use HistorisableActions;

    protected $notificationController;

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }

    public function index()
    {
        $announcements = Announcement::with(['reactions.user', 'user'])
            ->latest()
            ->get();

        if (auth()->user()->secteur === 'administration' ) {
            return view('announcements.dg-dashboard', compact('announcements'));
        }

        return view('announcements.employee-dashboard', compact('announcements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required'
        ]);

        // Create the announcement
        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => auth()->id()
        ]);

        // Notify all employees about the new announcement
        $this->notifyAllEmployees($announcement);

        // Record the action in history
        $user = auth()->user();
        $this->historiser("Une annonce '{$announcement->title}' a été créée par {$user->name}", 'create');

        return redirect()->route('announcements.index')
            ->with('success', 'Annonce créée avec succès');
    }

    /**
     * Send notification to all employees about the new announcement
     */
    private function notifyAllEmployees($announcement)
    {
        // Get all employees
        $employees = User::all();
        $user = auth()->user();
        foreach ($employees as $employee) {
            // Create notification request for each employee
            $notificationRequest = new Request([
                'recipient_id' => $employee->id,
                'subject' => 'Nouvelle annonce: ' . $announcement->title,
                'message' => "Annonce de {$user->name}" .
                             substr($announcement->content, 0, 200) .
                             (strlen($announcement->content) > 200 ? '...' : '')
            ]);

            // Send notification
            $this->notificationController->send($notificationRequest);
        }
    }


    public function storeReaction(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'comment' => 'required'
        ]);

        Reaction::create([
            'announcement_id' => $announcement->id,
            'user_id' => auth()->id(),
            'comment' => $validated['comment']
        ]);

        return back()->with('success', 'Réaction ajoutée avec succès');
    }
}
