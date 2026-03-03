<?php
/**
 * Court Controller
 */
class CourtController extends Controller {
    private $court;
    private $courtType;
    
    public function __construct() {
        parent::__construct();
        $this->court = new Court();
        $this->courtType = new CourtType();
    }
    
    public function index() {
        $query = $_GET['q'] ?? '';
        $filters = [
            'court_type' => $_GET['type'] ?? '',
            'city' => $_GET['city'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
        ];
        
        $courts = $this->court->search($query, $filters);
        $courtTypes = $this->courtType->getAllWithCounts();
        $cities = $this->court->getCities();
        
        $this->renderWithLayout('courts.index', [
            'title' => 'Browse Courts - ' . APP_NAME,
            'courts' => $courts,
            'courtTypes' => $courtTypes,
            'cities' => $cities,
            'query' => $query,
            'filters' => $filters,
        ]);
    }
    
    public function byType($slug) {
        $type = $this->courtType->findBySlug($slug);
        if (!$type) {
            $this->redirect('courts', 'Court type not found.', 'error');
        }
        
        $courts = $this->court->getByType($type['id']);
        
        $this->renderWithLayout('courts.by-type', [
            'title' => $type['name'] . ' Courts - ' . APP_NAME,
            'courtType' => $type,
            'courts' => $courts,
        ]);
    }
    
    public function show($id) {
        $court = $this->court->findWithType($id);
        if (!$court) {
            $this->redirect('courts', 'Court not found.', 'error');
        }
        
        // Parse amenities JSON
        $court['amenities_array'] = json_decode($court['amenities'] ?? '[]', true);
        
        // Get reviews
        $review = new Review();
        $reviews = $review->getByCourt($id);
        
        // Get today's schedule
        $today = date('Y-m-d');
        $slots = $this->court->getAvailableSlots($id, $today);
        
        $this->renderWithLayout('courts.show', [
            'title' => $court['name'] . ' - ' . APP_NAME,
            'court' => $court,
            'reviews' => $reviews,
            'slots' => $slots,
            'selectedDate' => $today,
        ]);
    }
    
    public function getSlots($id) {
        $date = $_GET['date'] ?? date('Y-m-d');
        
        $court = $this->court->find($id);
        if (!$court) {
            $this->json(['error' => 'Court not found'], 404);
        }
        
        $slots = $this->court->getAvailableSlots($id, $date);
        
        $this->json([
            'success' => true,
            'date' => $date,
            'slots' => $slots,
        ]);
    }
    
    public function calculatePrice($id) {
        return $_GET;
        $date = $_GET['date'] ?? date('Y-m-d');
        $startTime = $_GET['start_time'] ?? '';
        $endTime = $_GET['end_time'] ?? '';
        $isHalfCourt = isset($_GET['half_court']) && $_GET['half_court'] === '1';
        
        if (!$startTime || !$endTime) {
            $this->json(['error' => 'Start and end time required'], 400);
        }
        
        $price = $this->court->calculatePrice($id, $date, $startTime, $endTime, $isHalfCourt);
        
        if (!$price) {
            $this->json(['error' => 'Could not calculate price'], 400);
        }
        
        $this->json([
            'success' => true,
            'price' => $price,
            'formatted' => [
                'total' => formatPrice($price['total']),
                'downpayment' => formatPrice($price['downpayment']),
                'balance' => formatPrice($price['balance']),
            ]
        ]);
    }
    
    public function checkAvailability($id) {
        $date = $_GET['date'] ?? '';
        $startTime = $_GET['start_time'] ?? '';
        $endTime = $_GET['end_time'] ?? '';
        
        if (!$date || !$startTime || !$endTime) {
            $this->json(['error' => 'Date and times required'], 400);
        }
        
        $available = $this->court->isAvailable($id, $date, $startTime, $endTime);
        
        $this->json([
            'success' => true,
            'available' => $available,
        ]);
    }
}
