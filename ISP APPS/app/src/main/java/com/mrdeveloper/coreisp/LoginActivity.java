package com.mrdeveloper.coreisp;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.core.graphics.Insets;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputEditText;
import com.mrdeveloper.coreisp.api.RetrofitClient;
import com.mrdeveloper.coreisp.api.LoginResponse;
import java.util.HashMap;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LoginActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        MaterialButton btnLogin = findViewById(R.id.btnLogin);
        TextInputEditText etUsername = findViewById(R.id.etUsername);
        TextInputEditText etPassword = findViewById(R.id.etPassword);

        btnLogin.setOnClickListener(v -> {
            String username = etUsername.getText() != null ? etUsername.getText().toString() : "";
            String password = etPassword.getText() != null ? etPassword.getText().toString() : "";

            if (!username.isEmpty() && !password.isEmpty()) {
                HashMap<String, String> request = new HashMap<>();
                request.put("username", username);
                request.put("password", password);

                RetrofitClient.getInstance().login(request).enqueue(new Callback<LoginResponse>() {
                    @Override
                    public void onResponse(Call<LoginResponse> call, Response<LoginResponse> response) {
                        if (response.isSuccessful() && response.body() != null) {
                            SharedPreferences sharedPref = getSharedPreferences("ISP_APP", MODE_PRIVATE);
                            sharedPref.edit().putString("TOKEN", "Bearer " + response.body().getToken()).apply();
                            
                            startActivity(new Intent(LoginActivity.this, MainActivity.class));
                            finish();
                        } else {
                            Toast.makeText(LoginActivity.this, "Invalid Credentials", Toast.LENGTH_SHORT).show();
                        }
                    }

                    @Override
                    public void onFailure(Call<LoginResponse> call, Throwable t) {
                        // Navigate to Dashboard anyway for demo purposes if backend isn't connected
                        startActivity(new Intent(LoginActivity.this, MainActivity.class));
                        finish();
                    }
                });
            } else {
                Toast.makeText(this, "Please enter username and password", Toast.LENGTH_SHORT).show();
            }
        });
    }
}
