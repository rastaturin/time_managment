<?php namespace App\Http\Controllers;

use App\Exceptions\AccessDenied;
use App\Exceptions\BadRequest;
use App\Http\MyInput;
use App\Http\Requests;

use App\Timerow;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TimerowController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param $userId
     * @return Response
     */
    public function index($userId)
    {
        $user = $this->getAvailableUser($userId);
        $rows = Timerow::whereUser_id($user->id)->orderBy('date')->get();
        return $this->response($rows);
    }

    /**
     * Export
     * @param $userId
     * @param null $dateFrom
     * @param null $dateTill
     * @return Response
     */
    public function export($userId, $dateFrom = null, $dateTill = null)
    {
        $user = $this->getAvailableUser($userId);

        $rows = Timerow::whereUser_id($user->id)->dates($dateFrom, $dateTill)->orderBy('date')->get();
        $dates = [];
        foreach ($rows as $row) {
            if (!isset($dates[$row->date])) {
                $dates[$row->date] = ['time' => 0, 'notes' => []];
            }
            $dates[$row->date]['time'] += $row->duration;
            $dates[$row->date]['notes'][] = $row->note;
        }
        return view('export', ['dates' => $dates]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($userId)
    {
        $user = $this->getAvailableUser($userId);
        $timerow = new Timerow(MyInput::all());
        $user->timerows()->save($timerow);
        return $this->response($timerow);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $userId
     * @param $id
     * @return Response
     * @throws AccessDenied
     * @throws BadRequest
     */
    public function update($userId, $id)
    {
        $user = $this->getAvailableUser($userId);
        if (!($timerow = Timerow::find($id))) {
            throw new BadRequest(sprintf('%s timerow not found', $id));
        }
        if ($timerow->user->id != $user->id) {
            throw new AccessDenied("You can't update this row");
        }
        $timerow->update(MyInput::all());
        $this->response($timerow);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $userId
     * @return Response
     * @throws AccessDenied
     * @internal param int $id
     */
    public function destroy($userId, $id)
    {
        $user = $this->getAvailableUser($userId);
        if (!($timerow = Timerow::find($id))) {
            throw new BadRequest(sprintf('%s timerow not found', $id));
        }
        if ($timerow->user->id != $user->id) {
            throw new AccessDenied("You can't delete this row");
        }
        $timerow->delete();
        return $this->response(null);
    }

    /**
     * @param $userId
     * @return User
     * @throws AccessDenied
     */
    protected function getAvailableUser($userId)
    {
        $user = User::find($userId);
        if ($user && ($user->id == User::$logged->id || User::$logged->isAdmin())) {
            return $user;
        } else {
            throw new AccessDenied();
        }
    }
}
