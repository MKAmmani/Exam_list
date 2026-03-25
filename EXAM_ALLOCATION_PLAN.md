# Exam Venue Allocation System - Project Plan

## 📋 System Overview

**Faculty of Computing, Bayero University Kano**

An automated exam venue and seat allocation system designed to help exam officers efficiently allocate students to exam venues and seat numbers approximately 20 minutes before exam commencement.

---

## 🎯 Problem Statement

### Current Manual Process
1. Students register for courses at the beginning of each academic session
2. During exam period, exam officers receive lists of students per course
3. Exam officers manually:
   - Count students per course
   - Assign venues based on capacity
   - Write/assign seat numbers to each student
   - Print and display seating arrangements

**Challenges:**
- Time-consuming (done 20 minutes before exam)
- Prone to human error
- Difficult to balance venue capacity utilization
- Last-minute changes are chaotic

### Proposed Automated Solution
1. Exam Officers upload courses and enrolled students at the beginning of the session
2. System stores and manages this data throughout the session
3. At exam time, exam officer clicks one button to automatically allocate:
   - Venue assignment
   - Seat numbers
4. System generates printable seating arrangement lists

---

## 👥 User Roles

| Role | Responsibilities |
|------|-----------------|
| **Admin** | - Manage system settings<br>- Manage venues (add/edit/delete lecture halls/rooms)<br>- Manage exam officers<br>- View all allocation history<br>- Override allocations if needed |
| **Exam Officer** | - Upload/manage courses per academic session<br>- Upload students enrolled in each course<br>- Define exam schedules<br>- Run automatic venue/seat allocation<br>- Generate and print seating arrangement reports |
| **Student** | - Search exam venue and seat number (no login required)<br>- View personal exam schedule |

---

## 🗄️ Database Structure

### Core Tables

#### 1. `academic_sessions`
Stores academic session information
- `id`
- `name` (e.g., "2024/2025")
- `start_date`
- `end_date`
- `is_active` (boolean)
- `created_at`, `updated_at`

#### 2. `courses`
Stores course information
- `id`
- `code` (e.g., "COM401")
- `title` (e.g., "Software Engineering")
- `credit_hours`
- `semester` (1 or 2)
- `academic_session_id` (foreign key)
- `created_at`, `updated_at`

#### 3. `students`
Stores student information (extends existing Student model)
- `id`
- `name`
- `email`
- `registration_number` (e.g., "COM/4001/2021")
- `department_id` (foreign key)
- `level` (100, 200, 300, 400, etc.)
- `phone`
- `password`
- `remember_token`
- `created_at`, `updated_at`

#### 4. `course_students` (Pivot Table)
Many-to-many relationship between courses and students
- `id`
- `course_id` (foreign key)
- `student_id` (foreign key)
- `academic_session_id` (foreign key)
- `created_at`, `updated_at`

#### 5. `venues`
Stores exam venues (lecture halls, rooms)
- `id`
- `name` (e.g., "LT1", "Room 204")
- `type` (lecture_hall, classroom, hall)
- `capacity` (integer)
- `location` (e.g., "Faculty of Computing Building")
- `is_active` (boolean)
- `created_at`, `updated_at`

#### 6. `exams`
Stores exam schedule information
- `id`
- `course_id` (foreign key)
- `academic_session_id` (foreign key)
- `date`
- `start_time`
- `end_time`
- `duration_minutes`
- `created_at`, `updated_at`

#### 7. `allocations`
Stores venue and seat allocations for exams
- `id`
- `exam_id` (foreign key)
- `student_id` (foreign key)
- `venue_id` (foreign key)
- `seat_number` (e.g., "A-001", "LT1-001")
- `allocated_at` (timestamp)
- `allocated_by` (user_id - exam officer who ran allocation)
- `created_at`, `updated_at`

#### 8. `departments` (Already exists)
Stores department information
- `id`
- `name` (e.g., "Computer Science", "Information Technology")
- `code` (e.g., "COM", "IT")
- `created_at`, `updated_at`

---

## 🔄 Workflow

### Phase 1: Setup (Beginning of Session)
```
┌─────────────────────────────────────────────────────────────┐
│                    Exam Officer                             │
│                                                             │
│  1. Create/Activate Academic Session                        │
│  2. Upload Courses (bulk via CSV/Excel or manual entry)     │
│  3. Upload Students per Course (bulk or manual)             │
│  4. Define/Verify Venues and Capacities                     │
└─────────────────────────────────────────────────────────────┘
```

### Phase 2: Exam Time (20 Minutes Before Exam)
```
┌─────────────────────────────────────────────────────────────┐
│                    Exam Officer                             │
│                                                             │
│  1. Select the exam course                                  │
│  2. Click "Allocate Venues & Seats" button                  │
│  3. System automatically:                                   │
│     - Counts total students                                 │
│     - Selects appropriate venues based on capacity          │
│     - Assigns students to venues                            │
│     - Generates sequential seat numbers                     │
│  4. Review allocation (optional)                            │
│  5. Generate/Print seating arrangement list                 │
└─────────────────────────────────────────────────────────────┘
```

### Phase 3: Student Access (During Exam Period)
```
┌─────────────────────────────────────────────────────────────┐
│                      Student                                │
│                                                             │
│  1. Visit exam venue search page (no login)                 │
│  2. Enter Registration Number                               │
│  3. View:                                                   │
│     - Course code and title                                 │
│     - Exam date and time                                    │
│     - Assigned venue                                        │
│     - Seat number                                           │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎨 Features

### Admin Features
- [ ] Manage venues (CRUD operations)
- [ ] Manage exam officer accounts
- [ ] View all academic sessions
- [ ] View allocation history and reports
- [ ] Override/reallocate if needed
- [ ] System settings and configuration

### Exam Officer Features
- [ ] Manage academic sessions
- [ ] Upload/manage courses (bulk import via CSV/Excel)
- [ ] Upload/manage students per course (bulk import)
- [ ] Create exam schedules
- [ ] **One-click automatic allocation**
- [ ] View allocation results
- [ ] Generate reports:
  - Seating arrangement per venue
  - Student allocation list per course
  - Venue utilization report
  - Empty seats report

### Student Features
- [ ] Search exam venue by registration number
- [ ] View personal exam schedule with venue and seat

---

## 🔧 Allocation Algorithm

### Automatic Allocation Logic
```
INPUT: Course, Exam Date, Enrolled Students
OUTPUT: Venue assignments + Seat numbers

1. Get total number of students enrolled in course
2. Get available venues for the exam date/time
3. Sort venues by capacity (descending)
4. For each student:
   a. Assign to current venue if capacity not full
   b. Move to next venue if current is full
   c. Generate seat number (e.g., "A-001", "B-015")
5. Save all allocations to database
6. Generate reports
```

### Allocation Rules
- Students from same course should be in same venue if possible
- Seat numbers should be sequential and human-readable
- Venue capacity should not be exceeded
- Special needs students (if tracked) get priority seating

---

## 📊 Reports

### 1. Seating Arrangement List (Per Venue)
```
Venue: LT1 (Capacity: 100)
Exam: COM401 - Software Engineering
Date: 2025-06-15, 9:00 AM

Seat No. | Registration No. | Student Name
---------|------------------|------------------
A-001    | COM/4001/2021    | Ahmad Muhammad
A-002    | COM/4002/2021    | Fatima Aliyu
...
```

### 2. Student Allocation Slip (Printable)
```
BAYERO UNIVERSITY KANO
FACULTY OF COMPUTING
EXAM VENUE ALLOCATION

Name: Ahmad Muhammad
Reg. No: COM/4001/2021
Course: COM401 - Software Engineering
Date: 2025-06-15
Time: 9:00 AM
Venue: LT1
Seat: A-001
```

### 3. Venue Utilization Report
```
Session: 2024/2025
Total Venues: 15
Total Allocations: 2500
Average Utilization: 85%
```

---

## 🛠️ Technical Implementation

### Backend (Laravel)
- **Models:** AcademicSession, Course, Student, Venue, Exam, Allocation, Department
- **Controllers:** 
  - `AcademicSessionController`
  - `CourseController`
  - `StudentController`
  - `VenueController`
  - `ExamController`
  - `AllocationController`
- **Services:**
  - `AllocationService` (core allocation logic)
  - `ImportService` (CSV/Excel import)
- **Jobs:** (Optional) Queue job for large allocations

### Frontend (Vue.js + Inertia)
- **Pages:**
  - Admin Dashboard
  - Exam Officer Dashboard
  - Course Management
  - Student Management
  - Exam Schedule Management
  - Allocation Page
  - Reports Page
  - Student Search (public)

### File Imports
- Use Laravel Excel package (`maatwebsite/excel`)
- Support CSV and Excel formats
- Template downloads for easy data entry

---

## 📁 File Structure (New Files)

```
app/
├── Models/
│   ├── AcademicSession.php
│   ├── Course.php
│   ├── Venue.php
│   └── Allocation.php
├── Http/
│   ├── Controllers/
│   │   ├── AcademicSessionController.php
│   │   ├── CourseController.php
│   │   ├── StudentController.php
│   │   ├── VenueController.php
│   │   ├── ExamController.php
│   │   └── AllocationController.php
│   └── Requests/
│       ├── StoreCourseRequest.php
│       ├── ImportStudentsRequest.php
│       └── AllocateExamRequest.php
├── Services/
│   ├── AllocationService.php
│   └── ImportService.php
└── Exports/
    ├── StudentsExport.php
    └── AllocationsExport.php

database/
├── migrations/
│   ├── create_academic_sessions_table.php
│   ├── create_courses_table.php
│   ├── create_venues_table.php
│   ├── create_exams_table.php
│   ├── create_allocations_table.php
│   └── create_course_student_table.php
└── seeders/
    └── VenueSeeder.php

resources/
├── js/
│   └── Pages/
│       ├── Admin/
│       │   ├── Venues/
│       │   └── Dashboard.vue
│       ├── ExamOfficer/
│       │   ├── Courses/
│       │   ├── Students/
│       │   ├── Exams/
│       │   ├── Allocations/
│       │   └── Dashboard.vue
│       └── Student/
│           └── SearchVenue.vue
└── views/
    └── exports/
        └── seating-arrangement.blade.php

routes/
└── web.php (updated with new routes)
```

---

## 🚀 Development Phases

### ✅ Phase 1: Foundation (COMPLETED)
- [x] Create migrations for new tables
- [x] Create models with relationships
- [x] Seed initial venues data
- [x] Create AllocationService with auto-allocation algorithm
- [x] Update User model for exam officer role

### ✅ Phase 2: Backend API & CSV/Excel Import (COMPLETED)
- [x] Install Laravel Excel package
- [x] Create AcademicSessionController (CRUD + API)
- [x] Create CourseController with CSV/Excel import/export
- [x] Create StudentController with CSV/Excel import/export
- [x] Create ExamController for exam scheduling
- [x] Create AllocationController for venue allocation
- [x] Create ImportService for bulk student enrollment
- [x] Add API routes (38 endpoints)
- [x] Create API documentation

### 🔄 Phase 3: Frontend (Vue.js + Inertia) - NEXT
- [ ] Create Exam Officer Dashboard layout
- [ ] Create Academic Session management page
- [ ] Create Course management page with:
  - [ ] Course list view
  - [ ] Add/Edit course form
  - [ ] CSV/Excel import modal
  - [ ] Export functionality
  - [ ] Student enrollment per course
- [ ] Create Student management page with:
  - [ ] Student list view with search/filter
  - [ ] Add/Edit student form
  - [ ] CSV/Excel import modal
  - [ ] Export functionality
- [ ] Create Exam Schedule management page
- [ ] Create Allocation page with:
  - [ ] One-click allocate button
  - [ ] Allocation summary view
  - [ ] Venue breakdown display
  - [ ] Print seating arrangement
- [ ] Create public Student Venue Search page

### Phase 4: Admin Features
- [ ] Venue management (CRUD)
- [ ] User management (exam officers)
- [ ] System settings

### Phase 5: Testing & Polish
- [ ] Test allocation algorithm with various scenarios
- [ ] Test CSV/Excel import with edge cases
- [ ] Add validation error handling
- [ ] Add loading states and notifications
- [ ] Optimize for large datasets (1000+ students)

---

## ✅ Success Criteria

1. Exam officer can allocate 500+ students to venues in under 10 seconds
2. Students can find their venue without logging in
3. System handles multiple exams on same day/time
4. Venue capacity is never exceeded
5. Reports are printable and well-formatted

---

## 📝 Notes

- The system should be simple and fast - exam officers have only 20 minutes
- Minimal clicks to perform allocation
- Offline-capable (PWA consideration for future)
- Backup/export functionality for data safety
- Consider future integration with university's main system

---

**Document Version:** 1.0  
**Created:** March 22, 2025  
**Status:** Ready for Development
