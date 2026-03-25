# Exam Allocation System - API Documentation

## Base URL
```
http://localhost:8000/api
```

All API endpoints require authentication unless otherwise noted.

---

## Authentication

Use Laravel's default session-based authentication. Login at `/login` with:
- **Admin**: `admin@buk.edu.ng` / `password`
- **Exam Officer**: `examofficer@buk.edu.ng` / `password`

---

## Academic Sessions

### Get All Sessions
```http
GET /api/academic-sessions
```

### Get Active Session
```http
GET /api/academic-sessions/active
```

### Create Session
```http
POST /api/academic-sessions
Content-Type: application/json

{
    "name": "2024/2025",
    "start_date": "2024-09-01",
    "end_date": "2025-06-30",
    "is_active": true
}
```

### Set Active Session
```http
POST /api/academic-sessions/{id}/set-active
```

### Update Session
```http
PUT /api/academic-sessions/{id}
Content-Type: application/json

{
    "name": "2024/2025",
    "start_date": "2024-09-01",
    "end_date": "2025-06-30"
}
```

### Delete Session
```http
DELETE /api/academic-sessions/{id}
```

---

## Courses

### Get All Courses
```http
GET /api/courses?academic_session_id=1&semester=1&search=COM
```

### Get Course Template (for import)
```http
GET /api/courses/template
```

**Response:**
```json
{
    "template": {
        "headers": ["code", "title", "credit_hours", "semester"],
        "sample_data": [
            {"code": "COM401", "title": "Software Engineering", "credit_hours": 3, "semester": 1}
        ]
    }
}
```

### Import Courses from Excel/CSV
```http
POST /api/courses/import
Content-Type: multipart/form-data

file: [Excel/CSV file]
academic_session_id: 1
```

**Required Excel Columns:**
- `code` (required, unique)
- `title` (required)
- `credit_hours` (optional, default: 3)
- `semester` (optional, 1 or 2)

### Export Courses
```http
GET /api/courses/export?academic_session_id=1
```

### Create Course
```http
POST /api/courses
Content-Type: application/json

{
    "code": "COM401",
    "title": "Software Engineering",
    "credit_hours": 3,
    "semester": 1,
    "academic_session_id": 1
}
```

### Get Course Details
```http
GET /api/courses/{id}
```

### Update Course
```http
PUT /api/courses/{id}
Content-Type: application/json

{
    "title": "Advanced Software Engineering"
}
```

### Delete Course
```http
DELETE /api/courses/{id}
```

### Get Enrolled Students
```http
GET /api/courses/{id}/students?academic_session_id=1
```

### Enroll Students in Course
```http
POST /api/courses/{id}/enroll-students
Content-Type: multipart/form-data

file: [Excel/CSV file with registration_number column]
academic_session_id: 1
```

---

## Students

### Get All Students
```http
GET /api/students?department_id=1&level=400&search=Ahmad
```

### Get Student Template (for import)
```http
GET /api/students/template
```

**Response:**
```json
{
    "template": {
        "headers": ["name", "email", "registration_number", "department", "level", "phone"],
        "sample_data": [
            {
                "name": "Ahmad Muhammad",
                "email": "ahmad.muhammad@buk.edu.ng",
                "registration_number": "COM/4001/2021",
                "department": "Computer Science",
                "level": 400,
                "phone": "08012345678"
            }
        ]
    }
}
```

### Import Students from Excel/CSV
```http
POST /api/students/import
Content-Type: multipart/form-data

file: [Excel/CSV file]
department_id: 1 (optional)
```

**Required Excel Columns:**
- `name` (required)
- `registration_number` (required, unique)
- `email` (optional, unique)
- `department` (optional, name or code)
- `level` (optional, 100-500)
- `phone` (optional)

### Export Students
```http
GET /api/students/export?course_id=1&academic_session_id=1
```

### Create Student
```http
POST /api/students
Content-Type: application/json

{
    "name": "Ahmad Muhammad",
    "email": "ahmad.muhammad@buk.edu.ng",
    "registration_number": "COM/4001/2021",
    "department_id": 1,
    "level": 400,
    "phone": "08012345678"
}
```

### Get Student Details
```http
GET /api/students/{id}
```

### Update Student
```http
PUT /api/students/{id}
Content-Type: application/json

{
    "name": "Ahmad Ibrahim",
    "phone": "08098765432"
}
```

### Delete Student
```http
DELETE /api/students/{id}
```

---

## Public Student Venue Search (No Auth Required)

### Search by Registration Number
```http
POST /api/students/search-venue
Content-Type: application/json

{
    "registration_number": "COM/4001/2021"
}
```

**Response:**
```json
{
    "student": {
        "id": 1,
        "name": "Ahmad Muhammad",
        "registration_number": "COM/4001/2021"
    },
    "allocations": [
        {
            "course_code": "COM401",
            "course_title": "Software Engineering",
            "exam_date": "2025-06-15",
            "exam_time": "09:00:00",
            "venue": "LT1",
            "seat_number": "A-001"
        }
    ]
}
```

---

## Exams

### Get All Exams
```http
GET /api/exams?academic_session_id=1&course_id=1&date=2025-06-15&is_allocated=false
```

### Create Exam Schedule
```http
POST /api/exams
Content-Type: application/json

{
    "course_id": 1,
    "academic_session_id": 1,
    "date": "2025-06-15",
    "start_time": "09:00",
    "end_time": "11:00",
    "duration_minutes": 120
}
```

### Get Exam Details
```http
GET /api/exams/{id}
```

### Update Exam Schedule
```http
PUT /api/exams/{id}
Content-Type: application/json

{
    "date": "2025-06-16",
    "start_time": "14:00",
    "end_time": "16:00"
}
```

### Delete Exam Schedule
```http
DELETE /api/exams/{id}
```

### Get Students for Exam
```http
GET /api/exams/{id}/students
```

---

## Allocations (Core Feature)

### Allocate Students to Venues & Seats
```http
POST /api/allocations/exams/{examId}/allocate
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Successfully allocated 150 students to venues.",
    "allocations_count": 150,
    "venues_used": 3
}
```

**Response (Error):**
```json
{
    "success": false,
    "message": "Insufficient venue capacity. Need 200 seats, but only 150 available."
}
```

### Deallocate Exam (Remove All Allocations)
```http
POST /api/allocations/exams/{examId}/deallocate
```

### Get Allocation Summary
```http
GET /api/allocations/exams/{examId}/summary
```

**Response:**
```json
{
    "exam": {
        "id": 1,
        "course": "COM401",
        "date": "2025-06-15"
    },
    "summary": {
        "total_students": 150,
        "venues_used": 3,
        "venue_breakdown": {
            "LT1": {
                "venue_id": 1,
                "capacity": 150,
                "allocated": 100,
                "seats": [
                    {
                        "seat_number": "A-001",
                        "student_name": "Ahmad Muhammad",
                        "registration_number": "COM/4001/2021"
                    }
                ]
            }
        }
    }
}
```

### Get Student Allocation for Exam
```http
GET /api/allocations/exams/{examId}/students/{studentId}
```

### Get Venue Allocations for Exam
```http
GET /api/allocations/exams/{examId}/venues/{venueId}
```

### Print Seating Arrangement
```http
GET /api/allocations/exams/{examId}/venues/{venueId}/print
```

---

## Error Responses

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "message": "Unauthorized access. Admin privileges required."
}
```

### 404 Not Found
```json
{
    "message": "Resource not found."
}
```

### 422 Validation Error
```json
{
    "message": "Validation failed.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

### 500 Server Error
```json
{
    "success": false,
    "message": "Allocation failed: Database error."
}
```

---

## Excel/CSV Import Format

### Courses Template
| code | title | credit_hours | semester |
|------|-------|--------------|----------|
| COM401 | Software Engineering | 3 | 1 |
| COM402 | Database Systems | 3 | 1 |

### Students Template
| name | email | registration_number | department | level | phone |
|------|-------|----------------------|------------|-------|-------|
| Ahmad Muhammad | ahmad@buk.edu.ng | COM/4001/2021 | Computer Science | 400 | 08012345678 |

---

## Allocation Algorithm

The automatic allocation system:

1. **Counts** total students enrolled in the course
2. **Sorts** venues by capacity (descending)
3. **Distributes** students across venues:
   - Fills larger venues first
   - Moves to next venue when current is full
4. **Generates** seat numbers:
   - Format: `{Letter}-{Number}` (e.g., A-001, B-015)
   - Letter increments every 26 seats (A, B, C...)
   - Number is zero-padded to 3 digits

### Example Allocation
For 350 students:
- **LT1** (Capacity: 150): Seats A-001 to A-150
- **LT2** (Capacity: 150): Seats A-001 to A-150
- **LT3** (Capacity: 100): Seats A-001 to A-050

---

## Usage Workflow

### 1. Setup (Beginning of Session)
```bash
# 1. Create/activate academic session
POST /api/academic-sessions
POST /api/academic-sessions/{id}/set-active

# 2. Upload courses
POST /api/courses/import

# 3. Upload students
POST /api/students/import

# 4. Enroll students in courses
POST /api/courses/{id}/enroll-students
```

### 2. Exam Time (20 Minutes Before)
```bash
# 1. Create exam schedule
POST /api/exams

# 2. Allocate venues and seats
POST /api/allocations/exams/{id}/allocate

# 3. Get allocation summary
GET /api/allocations/exams/{id}/summary

# 4. Print seating arrangements
GET /api/allocations/exams/{id}/venues/{venueId}/print
```

### 3. Student Search (During Exam)
```bash
# Student searches venue (no login required)
POST /api/students/search-venue
```

---

## Rate Limiting
- API endpoints: 60 requests per minute
- Import endpoints: 10 requests per minute

---

## Support
For technical assistance, contact the Faculty of Computing IT Unit.
