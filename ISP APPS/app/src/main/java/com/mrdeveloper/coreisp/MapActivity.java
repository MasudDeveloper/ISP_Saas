package com.mrdeveloper.coreisp;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.core.graphics.Insets;
import com.google.android.material.button.MaterialButton;

public class MapActivity extends AppCompatActivity {

    private double customerLat = 23.8103;
    private double customerLng = 90.4125;
    private double tjBoxLat = 23.8115;
    private double tjBoxLng = 90.4110;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_map);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        MaterialButton btnNavigate = findViewById(R.id.btnNavigate);

        // Calculate straight line distance (Haversine formula representation)
        float[] results = new float[1];
        android.location.Location.distanceBetween(tjBoxLat, tjBoxLng, customerLat, customerLng, results);
        float distanceInMeters = results[0];

        // In a real app, we would load the GoogleMap object inside the FrameLayout 
        // using SupportMapFragment and plot the polylines here.

        btnNavigate.setOnClickListener(v -> {
            // Launch Google Maps Turn-by-Turn Navigation
            Uri gmmIntentUri = Uri.parse("google.navigation:q=" + customerLat + "," + customerLng + "&mode=d");
            Intent mapIntent = new Intent(Intent.ACTION_VIEW, gmmIntentUri);
            mapIntent.setPackage("com.google.android.apps.maps");

            if (mapIntent.resolveActivity(getPackageManager()) != null) {
                startActivity(mapIntent);
            } else {
                Toast.makeText(this, "Google Maps is not installed", Toast.LENGTH_SHORT).show();
            }
        });
    }
}
