<?php

class Chatbot
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = strtolower(trim($message));
    }

    public function getResponse(): string
{
    if ($this->contains(['hi', 'hello'])) {
        return "Hello 👋 I'm RoomLink Assistant. I can help you with rooms, bookings, and cancellations.";
    }

    if ($this->contains(['room', 'rooms', 'available'])) {
        return "To check available rooms, go to the Rooms page or use the search bar at the top to select dates and branch.";
    }

    if ($this->contains(['cheapest', 'price', 'cost'])) {
        return " The cheapest option is usually a Single Room. Prices may vary depending on the hotel branch and dates.";
    }

    if ($this->contains(['booking status', 'my booking', 'booking'])) {
        return " You can check your booking status by opening the Booking History page from your profile.";
    }

    if ($this->contains(['cancel', 'cancel booking'])) {
        return " To cancel a booking, go to Booking History, select your reservation, and click Cancel (before check-in date).";
    }

    if ($this->contains(['help', 'what can you do'])) {
        return " I can help you with:\n- Available rooms\n- Cheapest rooms\n- Booking status\n- Canceling bookings";
    }

    return " Sorry, I didn’t understand that. can i help you with anything else?";
}

    private function contains(array $keywords): bool
    {
        foreach ($keywords as $word) {
            if (strpos($this->message, $word) !== false) {
                return true;
            }
        }
        return false;
    }
}

// -------------------
// Handle request
// -------------------

$message = $_POST['message'] ?? '';

$chatbot = new Chatbot($message);
echo $chatbot->getResponse();
