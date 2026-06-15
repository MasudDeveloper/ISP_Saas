package com.mrdeveloper.coreisp;

import android.os.Bundle;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.core.graphics.Insets;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputEditText;

public class RouterControlActivity extends AppCompatActivity {

    private TextInputEditText etSsid;
    private TextInputEditText etPassword;
    private MaterialButton btnSave;
    private MaterialButton btnReboot;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_router_control);

        etSsid = findViewById(R.id.etSsid);
        etPassword = findViewById(R.id.etPassword);
        btnSave = findViewById(R.id.btnSave);
        btnReboot = findViewById(R.id.btnReboot);

        // Fetch current SSID from API (Mocking here)
        etSsid.setText("My_Home_WiFi");

        btnSave.setOnClickListener(v -> {
            String newSsid = etSsid.getText().toString();
            String newPass = etPassword.getText().toString();

            if (newSsid.isEmpty() || newPass.length() < 8) {
                Toast.makeText(this, "Valid SSID and min 8 char password required", Toast.LENGTH_SHORT).show();
                return;
            }

            // In a real app, make API call to Laravel /api/customer/wifi
            Toast.makeText(this, "Settings sent to Router. It will apply in 60s.", Toast.LENGTH_LONG).show();
        });

        btnReboot.setOnClickListener(v -> {
            Toast.makeText(this, "Reboot command sent.", Toast.LENGTH_SHORT).show();
        });
    }
}
