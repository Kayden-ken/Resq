<?php

namespace App\Services;

use App\Models\EmergencyContact;
use App\Models\EmergencyRequest;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Send emergency alert notification
     */
    public function sendEmergencyAlert(EmergencyRequest $request)
    {
        // In production: Send push notification via FCM
        // Send SMS via Twilio

        $this->createNotification([
            'user_id' => $request->requester_id,
            'type' => 'emergency_alert',
            'title' => 'Emergency Request Submitted',
            'message' => "Your {$request->incident_number} has been submitted. Help is on the way.",
            'data' => json_encode([
                'request_id' => $request->id,
                'incident_number' => $request->incident_number
            ])
        ]);

        return true;
    }

    /**
     * Send status change notification
     */
    public function notifyStatusChange(EmergencyRequest $request)
    {
        $statusMessages = [
            'pending' => 'Your emergency request is pending',
            'accepted' => 'A responder has accepted your request',
            'responding' => 'Responder is on the way',
            'arrived' => 'Responder has arrived at your location',
            'completed' => 'Emergency incident completed',
            'cancelled' => 'Your request has been cancelled',
        ];

        $message = $statusMessages[$request->status] ?? 'Status updated';

        $this->createNotification([
            'user_id' => $request->requester_id,
            'type' => 'status_change',
            'title' => 'Request Status Update',
            'message' => $message,
            'data' => json_encode([
                'request_id' => $request->id,
                'status' => $request->status
            ])
        ]);

        return true;
    }

    /**
     * Send to emergency contact
     */
    public function sendToContact(EmergencyContact $contact, EmergencyRequest $request)
    {
        // In production: Send SMS or push notification
        // This is a simplified version

        $message = "EMERGENCY ALERT: {$request->requester->name} has submitted an emergency request ({$request->incident_number}). " .
                   "Location: {$request->latitude}, {$request->longitude}. " .
                   "Type: {$request->emergencyType->name}";

        // Create notification record for contact
        // In production, contacts might not be app users, so we handle differently

        return true;
    }

    /**
     * Notify responder of new assignment
     */
    public function notifyResponderAssignment(User $responder, EmergencyRequest $request)
    {
        $this->createNotification([
            'user_id' => $responder->id,
            'type' => 'new_assignment',
            'title' => 'New Emergency Assignment',
            'message' => "You have been assigned to incident {$request->incident_number}",
            'data' => json_encode([
                'request_id' => $request->id,
                'incident_number' => $request->incident_number
            ])
        ]);

        return true;
    }

    /**
     * Create notification record
     */
    private function createNotification(array $data)
    {
        return Notification::create($data);
    }

    /**
     * Send push notification (placeholder for FCM)
     */
    public function sendPushNotification(User $user, string $title, string $body, array $data = [])
    {
        // In production, integrate with Firebase Cloud Messaging
        // $fcmToken = $user->fcm_token;
        // Use FCM to send notification

        return true;
    }

    /**
     * Send SMS (placeholder for Twilio)
     */
    public function sendSMS(string $to, string $message)
    {
        // In production, integrate with Twilio
        // $twilio = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));
        // $twilio->messages->create($to, ['from' => config('services.twilio.from'), 'body' => $message]);

        return true;
    }
}