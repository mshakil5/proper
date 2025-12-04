<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Booking::orderBy('status', 'asc')->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('full_name', fn($row) => $row->name)
                ->addColumn('date', fn($row) => Carbon::parse($row->created_at)->format('d-m-Y'))
                ->addColumn('status', function($row) {
                    $statusLabels = [0=>'Pending', 1=>'Approved', 2=>'Rejected'];
                    $badgeClass = [0=>'badge-warning', 1=>'badge-success', 2=>'badge-danger'];
                    return '<span class="badge '.$badgeClass[$row->status].'">'.$statusLabels[$row->status].'</span>';
                })
                ->addColumn('action', function($row) {
                    $buttons = '<button class="btn btn-sm btn-info view" data-id="'.$row->id.'"><i class="fas fa-eye"></i></button> ';
                    if ($row->status != 1) {
                        $buttons .= '<button class="btn btn-sm btn-success approve" data-id="'.$row->id.'"><i class="fas fa-check"></i></button> ';
                    }
                    if ($row->status != 2) {
                        $buttons .= '<button class="btn btn-sm btn-danger reject" data-id="'.$row->id.'"><i class="fas fa-times"></i></button> ';
                    }
                    $buttons .= '<button class="btn btn-sm btn-secondary delete" data-id="'.$row->id.'"><i class="fas fa-trash-alt"></i></button>';
                    return $buttons;
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.bookings.index');
    }

    public function show($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['status'=>404,'message'=>'Booking not found'],404);

        $booking->formatted_created_at = $booking->created_at->format('d-m-Y | H:i:s');

        $booking->menu_selection = $booking->menu_selection ? json_decode($booking->menu_selection, true) : [];

        return response()->json($booking);
    }

    public function destroy($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['success'=>false,'message'=>'Booking not found'],404);

        $booking->delete();
        return response()->json(['success'=>true,'message'=>'Booking deleted successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $booking = Booking::find($request->booking_id);
        if (!$booking) return response()->json(['status'=>404,'message'=>'Booking not found']);

        $booking->status = $request->status;
        $booking->save();

        $statusText = ['Pending','Approved','Rejected'][$booking->status];
        return response()->json(['status'=>200,'message'=>"Booking status updated to $statusText"]);
    }
}