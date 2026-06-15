package com.mrdeveloper.coreisp;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.core.graphics.Insets;
import com.google.android.material.card.MaterialCardView;
import com.mikhaellopez.circularprogressbar.CircularProgressBar;

public class MainActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        CircularProgressBar progressExpiry = findViewById(R.id.progressExpiry);
        MaterialCardView cardSpeedTest = findViewById(R.id.cardSpeedTest);
        MaterialCardView cardTicket = findViewById(R.id.cardTicket);
        MaterialCardView cardRouter = findViewById(R.id.cardRouter);
        // Set Progress with animation (Assuming total days = 30, left = 15)
        progressExpiry.setProgressWithAnimation(50f, 1500L);

        cardSpeedTest.setOnClickListener(v -> 
            startActivity(new Intent(MainActivity.this, SpeedTestActivity.class))
        );

        cardTicket.setOnClickListener(v -> 
            startActivity(new Intent(MainActivity.this, MapActivity.class))
        );

        cardRouter.setOnClickListener(v -> 
            startActivity(new Intent(MainActivity.this, RouterControlActivity.class))
        );
    }
}