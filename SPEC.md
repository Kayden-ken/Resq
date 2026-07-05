# ResQ - Emergency Response System Specification

## 1. Project Overview

**Project Name:** ResQ - Emergency Response System  
**Type:** Full-stack Laravel Web Application + API  
**Core Functionality:** A comprehensive emergency response platform enabling users to request emergency assistance, responders to manage incidents, and administrators to coordinate emergency services.  
**Target Users:** General public, emergency responders (police, fire, ambulance), administrators, dispatchers

---

## 2. Technology Stack

### Backend
- **Framework:** Laravel 11.x
- **PHP Version:** 8.2+
- **Database:** MySQL 8.0+
- **Cache:** Redis (for real-time features)
- **Queue:** Laravel Queue with Redis

### Frontend (Admin Dashboard)
- **Framework:** Laravel Blade + Livewire
- **Styling:** TailwindCSS
- **Charts:** Chart.js

### API (Mobile/External)
- **Format:** RESTful JSON API
- **Authentication:** Laravel Sanctum (Token-based)
- **Real-time:** Pusher (for notifications)

### External Services
- **Maps:** Google Maps API / OpenStreetMap
- **SMS:** Twilio / Plivo
- **Push Notifications:** FCM (Firebase Cloud Messaging)

---

## 3. Module Implementation Plan

### Phase 1: Core MVP (Modules 1-11, 17, 18, 22)
- User Authentication (Register, Login, Password Reset)
- User Profile Management
- Emergency Request Module
- Emergency Type Selection
- GPS Location Tracking
- Interactive Map Module
- Emergency Dispatch Module
- Route Navigation Module
- Real-Time Status Tracking
- SOS Quick Alert Module
- Emergency Contact Notification
- False Report Detection

### Phase 2: Communication (Modules 12-16, 19)
- Live Location Sharing
- Push Notifications
- In-App Messaging
- Voice Call Integration
- Media Upload
- Incident History

### Phase 3: Advanced Features (Modules 8, 20-32)
- Multi-Agency Coordination
- Emergency Facility Locator
- Responder Availability
- Offline Request
- Disaster Alert & Advisory
- Incident Report Management
- Reports & Analytics
- Admin Dashboard
- Responder Dashboard
- Announcement Module
- Feedback & Rating
- Audit Log
- System Settings

---

## 4. Database Schema Overview

### Core Tables
- `users` - All user types (public, responder, admin)
- `user_profiles` - Extended profile information
- `emergency_contacts` - User's emergency contacts
- `medical_info` - Medical details (allergies, blood type, conditions)
- `emergency_requests` - Main emergency incident records
- `emergency_types` - Predefined emergency categories
- `emergency_agencies` - Responding agencies
- `responders` - Emergency responder profiles
- `incident_responders` - Many-to-many: requests ↔ responders
- `messages` - In-app chat messages
- `media` - Uploaded photos/videos/recordings
- `facilities` - Emergency facilities (hospitals, stations)
- `announcements` - System announcements
- `incident_history` - Request timeline/history
- `feedback` - User ratings/feedback
- `audit_logs` - System activity logs
- `notifications` - Push notification records

---

## 5. API Endpoints Structure

### Authentication
- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `POST /api/auth/password/reset`
- `GET /api/auth/user`

### Users
- `GET /api/profile`
- `PUT /api/profile`
- `GET /api/emergency-contacts`
- `POST /api/emergency-contacts`
- `PUT /api/emergency-contacts/{id}`
- `DELETE /api/emergency-contacts/{id}`

### Emergency Requests
- `POST /api/emergency/request`
- `GET /api/emergency/requests`
- `GET /api/emergency/requests/{id}`
- `PUT /api/emergency/requests/{id}/status`
- `GET /api/emergency/types`
- `POST /api/emergency/sos`

### Responders
- `GET /api/responders`
- `PUT /api/responder/status`
- `GET /api/responder/assignments`
- `POST /api/responder/assignments/{id}/accept`
- `POST /api/responder/assignments/{id}/reject`

### Maps & Location
- `GET /api/facilities`
- `GET /api/facilities/{id}`
- `GET /api/nearby-facilities`
- `GET /api/route`

### Messaging
- `GET /api/messages/{request_id}`
- `POST /api/messages`
- `POST /api/media/upload`

### Admin
- `GET /api/admin/dashboard`
- `GET /api/admin/requests`
- `PUT /api/admin/requests/{id}`
- `GET /api/admin/users`
- `GET /api/admin/responders`
- `GET /api/admin/reports`
- `POST /api/admin/announcements`
- `GET /api/admin/audit-logs`

---

## 6. Project Structure

```
ResQ/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   ├── Web/
│   │   │   └── Admin/
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/
│   ├── Providers/
│   └── Services/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── public/
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   ├── auth/
│   │   └── layouts/
│   ├── css/
│   └── js/
├── routes/
├── tests/
└── vendor/
```

---

## 7. Acceptance Criteria

### Phase 1 Completion
- [ ] Users can register, login, and manage profiles
- [ ] Users can submit emergency requests with type and location
- [ ] SOS button works with single tap/press
- [ ] Emergency contacts are notified automatically
- [ ] Responders receive and can accept/reject requests
- [ ] Real-time status updates work
- [ ] Admin can view all requests and manage users
- [ ] Basic dashboard shows statistics

### Security Requirements
- All API endpoints authenticated (except public ones)
- HTTPS only in production
- Rate limiting on emergency endpoints
- Input validation and sanitization
- SQL injection protection (Eloquent ORM)
- XSS protection (Blade escaping)

---

## 8. Configuration

Environment variables required:
```
APP_NAME=ResQ
APP_ENV=local
APP_KEY=base64:...
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=resq
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=

GOOGLE_MAPS_API_KEY=

FCM_SERVER_KEY=
```

---

*Document Version: 1.0*  
*Last Updated: 2026-07-03*