<?php
/**
 * Home Controller
 */
class HomeController extends Controller {
    public function index() {
        $courtType = new CourtType();
        $court = new Court();
        
        $courtTypes = $courtType->getAllWithCounts();
        $featuredCourts = $court->getAllActive();
        $cities = $court->getCities();
        
        $this->renderWithLayout('home.index', [
            'title' => APP_NAME /**. ' - Book Sports Courts in the Philippines'*/,
            'courtTypes' => $courtTypes,
            'featuredCourts' => array_slice($featuredCourts, 0, 6),
            'cities' => $cities,
        ]);
    }
    
    public function about() {
        $this->renderWithLayout('home.about', [
            'title' => 'About Us - ' . APP_NAME
        ]);
    }
    
    public function contact() {
        $this->renderWithLayout('home.contact', [
            'title' => 'Contact Us - ' . APP_NAME
        ]);
    }
    
    public function terms() {
        $this->renderWithLayout('home.terms', [
            'title' => 'Terms of Service - ' . APP_NAME
        ]);
    }
    
    public function privacy() {
        $this->renderWithLayout('home.privacy', [
            'title' => 'Privacy Policy - ' . APP_NAME
        ]);
    }
}
