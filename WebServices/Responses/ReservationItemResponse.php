<?php

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class ReservationItemResponse extends RestResponse
{
    public $referenceNumber;
    public $startDate;
    public $endDate;
    public $firstName;
    public $lastName;
    public $resourceName;
    public $title;
    public $description;
    public $requiresApproval;
    public $isRecurring;
    public $scheduleId;
    public $userId;
    public $resourceId;
    public $duration;
    public $bufferTime;
    public $bufferedStartDate;
    public $bufferedEndDate;
    public $participants = [];
    public $invitees = [];
    public $participatingGuests = [];
    public $invitedGuests = [];
    public $startReminder;
    public $endReminder;
    public $color;
    public $textColor;
    public $checkInDate;
    public $checkOutDate;
    public $originalEndDate;
    public $isCheckInEnabled;
    public $autoReleaseMinutes;
    public $resourceStatusId;
    public $creditsConsumed;

    public function __construct(ReservationItemView $reservationItemView, IRestServer $server, $showUser, $showDetails)
    {
        $this->referenceNumber = $reservationItemView->ReferenceNumber;
        $this->startDate = $reservationItemView->StartDate->ToIso();
        $this->endDate = $reservationItemView->EndDate->ToIso();
        $this->duration = $reservationItemView->GetDuration()->__toString();
        $this->resourceName = $reservationItemView->ResourceName;

        if ($showUser) {
            $this->firstName = $reservationItemView->FirstName;
            $this->lastName = $reservationItemView->LastName;
            $this->participants = $reservationItemView->ParticipantNames;
            $this->invitees = $reservationItemView->InviteeNames;
            $this->participatingGuests = $reservationItemView->ParticipatingGuests;
            $this->invitedGuests = $reservationItemView->InvitedGuests;
        }

        if ($showDetails) {
            $this->title = $reservationItemView->Title;
            $this->description = $reservationItemView->Description;
        }

        $this->requiresApproval = (bool)$reservationItemView->RequiresApproval;
        $this->isRecurring = (bool)$reservationItemView->IsRecurring;

        $this->scheduleId = $reservationItemView->ScheduleId;
        $this->userId = $reservationItemView->UserId;
        $this->resourceId = $reservationItemView->ResourceId;
        $this->bufferTime = $reservationItemView->GetBufferTime()->__toString();
        $bufferedDuration = $reservationItemView->BufferedTimes();
        $this->bufferedStartDate = $bufferedDuration->GetBegin()->ToIso();
        $this->bufferedEndDate = $bufferedDuration->GetEnd()->ToIso();
        $this->resourceStatusId = $reservationItemView->ResourceStatusId;

        if ($reservationItemView->StartReminder != null) {
            $this->startReminder = $reservationItemView->StartReminder->MinutesPrior();
        }

        if ($reservationItemView->EndReminder != null) {
            $this->endReminder = $reservationItemView->EndReminder->MinutesPrior();
        }

        $this->color = $reservationItemView->GetColor();
        $this->textColor = $reservationItemView->GetTextColor();
        $this->checkInDate = $reservationItemView->CheckinDate->ToIso();
        $this->checkOutDate = $reservationItemView->CheckoutDate->ToIso();
        $this->originalEndDate = $reservationItemView->OriginalEndDate->ToIso();
        $this->isCheckInEnabled = $reservationItemView->IsCheckInEnabled;
        $this->autoReleaseMinutes = $reservationItemView->AutoReleaseMinutes;
        $this->creditsConsumed = $reservationItemView->CreditsConsumed;

        $this->AddService(
            $server,
            WebServices::GetResource,
            [WebServiceParams::ResourceId => $reservationItemView->ResourceId]
        );
        $this->AddService(
            $server,
            WebServices::GetReservation,
            [WebServiceParams::ReferenceNumber => $reservationItemView->ReferenceNumber]
        );
        $this->AddService(
            $server,
            WebServices::GetUser,
            [WebServiceParams::UserId => $reservationItemView->UserId]
        );
        $this->AddService(
            $server,
            WebServices::GetSchedule,
            [WebServiceParams::ScheduleId => $reservationItemView->ScheduleId]
        );
    }

    public static function Example()
    {
        return new ExampleReservationItemResponse();
    }
}

class ExampleReservationItemResponse extends ReservationItemResponse
{
    public function __construct()
    {
        $this->description = 'reservation description';
        $this->endDate = Date::Now()->ToIso();
        $this->firstName = 'first';
        $this->isRecurring = true;
        $this->lastName = 'last';
        $this->referenceNumber = 'refnum';
        $this->requiresApproval = true;
        $this->resourceId = 123;
        $this->resourceName = 'resourcename';
        $this->scheduleId = 22;
        $this->startDate = Date::Now()->ToIso();
        $this->title = 'reservation title';
        $this->userId = 11;
        $this->participants = ['participant name'];
        $this->invitees = ['invitee name'];
        $this->autoReleaseMinutes = 1;
        $this->bufferedStartDate = Date::Now()->ToIso();
        $this->bufferedEndDate = Date::Now()->ToIso();
        $this->bufferTime = TimeInterval::FromMinutes(1.5)->__toString();
        $this->checkInDate = Date::Now()->ToIso();
        $this->checkOutDate = Date::Now()->ToIso();
        $this->originalEndDate = Date::Now()->ToIso();
        $this->color = '#FFFFFF';
        $this->duration = DateDiff::FromTimeString('1:45')->__toString();
        $this->endReminder = 10;
        $this->isCheckInEnabled = true;
        $this->startReminder = 10;
        $this->textColor = '#000000';
        $this->creditsConsumed = 15;
    }
}
