<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Messages extends Component
{
    public $selectedConversation = 0, $message = null, $conversations = [], $messages = [];

    public function mount($id = 0)
    {
        $this->selectConversation($id);
        $this->getConversations();
    }

    public function render()
    {
        return view('livewire.messages');
    }

    public function getConversations()
    {
        $authUserId = Auth::id();
        $this->conversations = User::Where('id', $this->selectedConversation)
            ->orWhereHas('messagesFrom', function ($query) use ($authUserId) {
                $query->where('to', $authUserId);
            })
            ->orWhereHas('messagesTo', function ($query) use ($authUserId) {
                $query->where('from', $authUserId);
            })
            ->distinct()->get();
    }

    public function selectConversation($id)
    {
        $this->selectedConversation = $id;
        $this->getMessages();
        $this->dispatch('scrollMessagesToEnd');
    }

    public function sendMessage()
    {
        if ($this->message) {
            Message::create([
                'from' => auth()->id(),
                'to' => $this->selectedConversation,
                'message' => $this->message
            ]);
            $this->message = null;
            $this->getMessages();
            $this->dispatch('scrollMessagesToEnd');
            // User::find($this->selectedConversation)
            //     ->notify(new \App\Notifications\GeneralNotification(
            //         'message',
            //         auth()->id()
            //     ));
        }
    }
    public function getMessages()
    {
        if ($this->selectedConversation == 0) {
            return;
        }
        $count = count($this->messages);
        $authUserId = Auth::id();
        $this->messages = Message::where(function ($query) use ($authUserId) {
            $query->where('from', $authUserId)->where('to', $this->selectedConversation);
        })->orWhere(function ($query) use ($authUserId) {
            $query->where('from', $this->selectedConversation)->where('to', $authUserId);
        })->get();
        if (count($this->messages) > $count) {
            $this->dispatch('scrollMessagesToEnd');
        }
    }
}
