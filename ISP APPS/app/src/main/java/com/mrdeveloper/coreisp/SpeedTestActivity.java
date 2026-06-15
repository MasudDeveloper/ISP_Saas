package com.mrdeveloper.coreisp;

import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.widget.TextView;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.core.graphics.Insets;
import com.google.android.material.button.MaterialButton;
import com.mikhaellopez.circularprogressbar.CircularProgressBar;

public class SpeedTestActivity extends AppCompatActivity {

    private CircularProgressBar speedProgressBar;
    private TextView tvSpeedVal;
    private TextView tvPing;
    private TextView tvUpload;
    private MaterialButton btnStartTest;
    private Handler handler = new Handler(Looper.getMainLooper());

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_speed_test);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        speedProgressBar = findViewById(R.id.speedProgressBar);
        tvSpeedVal = findViewById(R.id.tvSpeedVal);
        tvPing = findViewById(R.id.tvPing);
        tvUpload = findViewById(R.id.tvUpload);
        btnStartTest = findViewById(R.id.btnStartTest);

        speedProgressBar.setProgressMax(100f);

        btnStartTest.setOnClickListener(v -> startMockSpeedTest());
    }

    private void startMockSpeedTest() {
        btnStartTest.setEnabled(false);
        btnStartTest.setText("TESTING...");
        tvPing.setText("12 ms");
        
        // Mocking Speed Test Animation (Download)
        new Thread(() -> {
            for (int i = 0; i <= 45; i++) {
                final int speed = i;
                handler.post(() -> {
                    speedProgressBar.setProgress((float) speed);
                    tvSpeedVal.setText(String.valueOf(speed));
                });
                try { Thread.sleep(50); } catch (InterruptedException e) { e.printStackTrace(); }
            }
            
            handler.post(() -> {
                tvUpload.setText("20 Mbps");
                btnStartTest.setText("TEST AGAIN");
                btnStartTest.setEnabled(true);
            });
        }).start();
    }
}
