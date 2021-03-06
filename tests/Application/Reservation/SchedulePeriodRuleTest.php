<?php

require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');

class SchedulePeriodRuleTests extends TestBase
{
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;

    /**
     * @var IScheduleLayout
     */
    private $layout;

    /**
     * @var SchedulePeriodRule
     */
    private $rule;

    public function setUp(): void
    {
        parent::setup();

        $this->scheduleRepository = $this->createMock('IScheduleRepository');
        $this->layout = $this->createMock('IScheduleLayout');

        $this->rule = new SchedulePeriodRule($this->scheduleRepository, $this->fakeUser);
    }

    public function testFailsWhenStartTimeDoesNotMatchPeriod()
    {
        $date = Date::Now();
        $dates = new DateRange($date, $date);
        $scheduleId = 1232;
        $resource = new FakeBookableResource(1);
        $resource->SetScheduleId($scheduleId);

        $series = ReservationSeries::Create(1, $resource, null, null, $dates, new RepeatNone(), $this->fakeUser);

        $this->scheduleRepository
                ->expects($this->once())
                ->method('GetLayout')
                ->with(
                    $this->equalTo($scheduleId),
                    $this->equalTo(new ScheduleLayoutFactory($this->fakeUser->Timezone))
                )
                ->will($this->returnValue($this->layout));

        $this->layout
                ->expects($this->at(0))
                ->method('GetPeriod')
                ->with($this->equalTo($series->CurrentInstance()->StartDate()))
                ->will($this->returnValue(new SchedulePeriod($date, $date->AddMinutes(1))));

        $result = $this->rule->Validate($series, null);

        $this->assertFalse($result->IsValid());
    }

    public function testFailsWhenEndTimeDoesNotMatchPeriod()
    {
        $date = Date::Now();
        $dates = new DateRange($date, $date);
        $scheduleId = 1232;
        $resource = new FakeBookableResource(1);
        $resource->SetScheduleId($scheduleId);

        $series = ReservationSeries::Create(1, $resource, null, null, $dates, new RepeatNone(), $this->fakeUser);

        $this->scheduleRepository
                ->expects($this->once())
                ->method('GetLayout')
                ->with(
                    $this->equalTo($scheduleId),
                    $this->equalTo(new ScheduleLayoutFactory($this->fakeUser->Timezone))
                )
                ->will($this->returnValue($this->layout));

        $this->layout
                ->expects($this->at(0))
                ->method('GetPeriod')
                ->with($this->equalTo($series->CurrentInstance()->StartDate()))
                ->will($this->returnValue(new SchedulePeriod($date, $date)));

        $this->layout
                ->expects($this->at(1))
                ->method('GetPeriod')
                ->with($this->equalTo($series->CurrentInstance()->EndDate()))
                ->will($this->returnValue(new SchedulePeriod($date->AddMinutes(1), $date)));

        $result = $this->rule->Validate($series, null);

        $this->assertFalse($result->IsValid());
    }

    public function testSucceedsWhenStartAndEndTimeMatchPeriods()
    {
        $date = Date::Now();
        $dates = new DateRange($date, $date);
        $scheduleId = 1232;
        $resource = new FakeBookableResource(1);
        $resource->SetScheduleId($scheduleId);

        $series = ReservationSeries::Create(1, $resource, null, null, $dates, new RepeatNone(), $this->fakeUser);

        $this->scheduleRepository
                ->expects($this->once())
                ->method('GetLayout')
                ->with(
                    $this->equalTo($scheduleId),
                    $this->equalTo(new ScheduleLayoutFactory($this->fakeUser->Timezone))
                )
                ->will($this->returnValue($this->layout));

        $period = new SchedulePeriod($date, $date);
        $this->layout
                ->expects($this->at(0))
                ->method('GetPeriod')
                ->with($this->equalTo($series->CurrentInstance()->StartDate()))
                ->will($this->returnValue($period));

        $this->layout
                ->expects($this->at(1))
                ->method('GetPeriod')
                ->with($this->equalTo($series->CurrentInstance()->EndDate()))
                ->will($this->returnValue($period));

        $result = $this->rule->Validate($series, null);

        $this->assertTrue($result->IsValid());
    }

    public function testDoesNotEvenCheckIfTheDatesHaveNotBeenChanged()
    {
        $dates = new DateRange(Date::Now(), Date::Now());

        $series = new ExistingReservationSeries();
        $current = new Reservation($series, $dates, 123);
        $current->SetReservationDate($dates);
        $series->WithCurrentInstance($current);

        $result = $this->rule->Validate($series, null);
        $this->assertTrue($result->IsValid());
    }
}
