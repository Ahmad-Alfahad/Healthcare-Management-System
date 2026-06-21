<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $patient_id
 * @property int $doctor_id
 * @property string $status
 * @property string $reason
 * @property string $scheduled_date
 * @property string $start_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Doctor $doctor
 * @property-read \App\Models\Patient $patient
 * @property-read \App\Models\Visit|null $visit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereScheduledDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereUpdatedAt($value)
 */
	class Appointment extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $table_name
 * @property string $action
 * @property int $record_id
 * @property string $old_value
 * @property string $new_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereNewValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereOldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereTableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 */
	class Department extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $visit_id
 * @property string $diagnosis_code
 * @property string $description
 * @property string $diagnosis_type
 * @property string $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Visit|null $visit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis whereDiagnosisCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis whereDiagnosisType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diagnosis whereVisitId($value)
 */
	class Diagnosis extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $prescription_item_id
 * @property int $pharmacist_id
 * @property int $quantity_dispensed
 * @property string $dispensed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pharmacist $pharmacist
 * @property-read \App\Models\PrescriptionItem $prescriptionItem
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispensing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispensing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispensing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispensing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispensing whereDispensedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispensing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispensing wherePharmacistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispensing wherePrescriptionItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispensing whereQuantityDispensed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispensing whereUpdatedAt($value)
 */
	class Dispensing extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $facility_department_specialization_id
 * @property int $profile_id
 * @property string $qualification
 * @property int $years_of_experience
 * @property string|null $biography
 * @property string|null $achievements
 * @property string|null $languages
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Appointment> $appointments
 * @property-read int|null $appointments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DoctorSchedule> $doctorSchedule
 * @property-read int|null $doctor_schedule_count
 * @property-read \App\Models\Profile $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Visit> $visits
 * @property-read int|null $visits_count
 * @property-read \App\Models\FacilityDepartmentSpecialization $workConfiguration
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereAchievements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereBiography($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereFacilityDepartmentSpecializationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereLanguages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereQualification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereYearsOfExperience($value)
 */
	class Doctor extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $doctor_id
 * @property string $day_of_week
 * @property int $is_off
 * @property string $start_time
 * @property string $end_time
 * @property int $avg_consultation_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Doctor $doctor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule whereAvgConsultationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule whereIsOff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorSchedule whereUpdatedAt($value)
 */
	class DoctorSchedule extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $facility_type
 * @property string $phone_number
 * @property string $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Facility> $children
 * @property-read int|null $children_count
 * @property-read Facility|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereFacilityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereUpdatedAt($value)
 */
	class Facility extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $facility_id
 * @property int $department_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Department $department
 * @property-read \App\Models\Facility $facility
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartment whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartment whereFacilityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartment whereUpdatedAt($value)
 */
	class FacilityDepartment extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $facility_department_id
 * @property int $specialization_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FacilityDepartment $facilityDepartment
 * @property-read \App\Models\Specialization $specialization
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartmentSpecialization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartmentSpecialization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartmentSpecialization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartmentSpecialization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartmentSpecialization whereFacilityDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartmentSpecialization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartmentSpecialization whereSpecializationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityDepartmentSpecialization whereUpdatedAt($value)
 */
	class FacilityDepartmentSpecialization extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $visit_id
 * @property int $lab_test_id
 * @property string $requested_at
 * @property string $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LabResult|null $labResult
 * @property-read \App\Models\LabTest $labTest
 * @property-read \App\Models\Visit $visit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabRequestItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabRequestItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabRequestItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabRequestItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabRequestItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabRequestItem whereLabTestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabRequestItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabRequestItem whereRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabRequestItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabRequestItem whereVisitId($value)
 */
	class LabRequestItem extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $lab_request_item_id
 * @property int $lab_staff_id
 * @property string $notes
 * @property string $status
 * @property numeric $value
 * @property string $unit
 * @property string $reference_range
 * @property string|null $access_token
 * @property string $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LabRequestItem $labRequestItem
 * @property-read \App\Models\LabStaff|null $labStaff
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereLabRequestItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereLabStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereReferenceRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabResult whereValue($value)
 */
	class LabResult extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $facility_id
 * @property int $profile_id
 * @property string $specialization
 * @property string $degree
 * @property int $years_of_experience
 * @property string|null $license_number
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Facility $facility
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LabResult> $labResults
 * @property-read int|null $lab_results_count
 * @property-read \App\Models\Profile $profile
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff whereFacilityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff whereLicenseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff whereSpecialization($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabStaff whereYearsOfExperience($value)
 */
	class LabStaff extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $range_high
 * @property int $range_low
 * @property string $unit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LabRequestItem> $labRequestItems
 * @property-read int|null $lab_request_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTest whereRangeHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTest whereRangeLow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTest whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LabTest whereUpdatedAt($value)
 */
	class LabTest extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PatientMedicalCondition> $patientMedicalConditions
 * @property-read int|null $patient_medical_conditions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCondition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCondition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCondition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCondition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCondition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCondition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCondition whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCondition whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCondition whereUpdatedAt($value)
 */
	class MedicalCondition extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $profile_id
 * @property string|null $blood_type
 * @property numeric|null $height
 * @property numeric|null $weight
 * @property string|null $allergies
 * @property string|null $chronic_diseases
 * @property string|null $medical_history
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_phone
 * @property string|null $emergency_contact_relation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Appointment> $appointments
 * @property-read int|null $appointments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PatientMedicalCondition> $patientMedicalConditions
 * @property-read int|null $patient_medical_conditions_count
 * @property-read \App\Models\Profile $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Visit> $visits
 * @property-read int|null $visits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereAllergies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereBloodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereChronicDiseases($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereEmergencyContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereEmergencyContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereEmergencyContactRelation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereMedicalHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereWeight($value)
 */
	class Patient extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $patient_id
 * @property int $medical_condition_id
 * @property \Illuminate\Support\Carbon|null $diagnosed_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MedicalCondition $medicalCondition
 * @property-read \App\Models\Patient $patient
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientMedicalCondition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientMedicalCondition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientMedicalCondition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientMedicalCondition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientMedicalCondition whereDiagnosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientMedicalCondition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientMedicalCondition whereMedicalConditionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientMedicalCondition whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientMedicalCondition wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientMedicalCondition whereUpdatedAt($value)
 */
	class PatientMedicalCondition extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $facility_id
 * @property int $profile_id
 * @property string $degree
 * @property int $years_of_experience
 * @property string|null $license_number
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dispensing> $dispensings
 * @property-read int|null $dispensings_count
 * @property-read \App\Models\Facility $facility
 * @property-read \App\Models\Profile $profile
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist whereFacilityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist whereLicenseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pharmacist whereYearsOfExperience($value)
 */
	class Pharmacist extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $visit_id
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PrescriptionItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Visit $visit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereVisitId($value)
 */
	class Prescription extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $prescription_id
 * @property string $medication_name
 * @property string $dosage
 * @property string $quantity_prescribed
 * @property string $frequency
 * @property string $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dispensing> $dispensings
 * @property-read int|null $dispensings_count
 * @property-read \App\Models\Prescription $prescription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem whereDosage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem whereMedicationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem wherePrescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem whereQuantityPrescribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionItem whereUpdatedAt($value)
 */
	class PrescriptionItem extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $full_name
 * @property string|null $national_number
 * @property string|null $phone
 * @property string|null $gender
 * @property string|null $address
 * @property string|null $date_of_birth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Patient|null $patient
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereNationalNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUserId($value)
 */
	class Profile extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereUpdatedAt($value)
 */
	class Specialization extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Profile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Illuminate\Database\Eloquent\Model {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $appointment_id
 * @property int $doctor_id
 * @property int $patient_id
 * @property string $status
 * @property string|null $notes
 * @property string $visited_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Appointment $appointment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Diagnosis> $diagnoses
 * @property-read int|null $diagnoses_count
 * @property-read \App\Models\Doctor $doctor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LabRequestItem> $labRequestItems
 * @property-read int|null $lab_request_items_count
 * @property-read \App\Models\Patient $patient
 * @property-read \App\Models\Prescription|null $prescription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereAppointmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereVisitedAt($value)
 */
	class Visit extends \Illuminate\Database\Eloquent\Model {}
}

