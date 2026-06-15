<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FtpMediaController extends Controller
{
    /**
     * Return list of movies from Local FTP / BDIX Server
     */
    public function getMovies(Request $request)
    {
        // In a real scenario, this might query an internal database that indexes the FTP,
        // or directly scrape the FTP directory using an adapter.

        $movies = [
            [
                'id' => 1,
                'title' => 'Dune: Part Two',
                'category' => 'Sci-Fi / Action',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2JGqqUT10.jpg',
                'ftp_link' => 'ftp://media.localisp.net/Movies/Hollywood/Dune_Part_Two_1080p.mkv',
                'trailer_url' => 'https://www.youtube.com/watch?v=Way9Dexny3w',
                'year' => 2024,
                'rating' => 8.8
            ],
            [
                'id' => 2,
                'title' => 'Toofan (2024)',
                'category' => 'Bangla Action',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/toofan_bangla.jpg', // Mock URL
                'ftp_link' => 'ftp://media.localisp.net/Movies/Bangla/Toofan_1080p.mkv',
                'trailer_url' => 'https://www.youtube.com/watch?v=example',
                'year' => 2024,
                'rating' => 9.0
            ],
            [
                'id' => 3,
                'title' => 'Oppenheimer',
                'category' => 'Biography / Drama',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
                'ftp_link' => 'ftp://media.localisp.net/Movies/Hollywood/Oppenheimer_4K.mkv',
                'trailer_url' => 'https://www.youtube.com/watch?v=uYPbbksJxIg',
                'year' => 2023,
                'rating' => 8.6
            ]
        ];

        return response()->json([
            'success' => true,
            'server_status' => 'Online',
            'server_speed' => '1 Gbps (Local)',
            'data' => $movies
        ]);
    }
}
