<?php

namespace Tests\Unit;

use App\Repositories\DepartmentRepository;
use App\Repositories\SpecializationRepository;
use App\Services\DepartmentService;
use App\Services\SpecializationService;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class DepartmentSpecializationDeletionTest extends TestCase
{
    public function test_unassigned_department_can_be_deleted(): void
    {
        /** @var DepartmentRepository&MockObject $repository */
        $repository = $this->createMock(DepartmentRepository::class);
        $repository->expects(self::once())
            ->method('hasFacilityAssignments')
            ->with(1)
            ->willReturn(false);
        $repository->expects(self::once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        self::assertTrue((new DepartmentService($repository))->deleteDepartment(1));
    }

    public function test_assigned_department_cannot_be_deleted(): void
    {
        /** @var DepartmentRepository&MockObject $repository */
        $repository = $this->createMock(DepartmentRepository::class);
        $repository->expects(self::once())
            ->method('hasFacilityAssignments')
            ->with(1)
            ->willReturn(true);
        $repository->expects(self::never())->method('delete');

        $this->expectException(ValidationException::class);

        (new DepartmentService($repository))->deleteDepartment(1);
    }

    public function test_unassigned_specialization_can_be_deleted(): void
    {
        /** @var SpecializationRepository&MockObject $repository */
        $repository = $this->createMock(SpecializationRepository::class);
        $repository->expects(self::once())
            ->method('hasAssignments')
            ->with(1)
            ->willReturn(false);
        $repository->expects(self::once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        self::assertTrue((new SpecializationService($repository))->deleteSpecialization(1));
    }

    public function test_assigned_specialization_cannot_be_deleted(): void
    {
        /** @var SpecializationRepository&MockObject $repository */
        $repository = $this->createMock(SpecializationRepository::class);
        $repository->expects(self::once())
            ->method('hasAssignments')
            ->with(1)
            ->willReturn(true);
        $repository->expects(self::never())->method('delete');

        $this->expectException(ValidationException::class);

        (new SpecializationService($repository))->deleteSpecialization(1);
    }
}
