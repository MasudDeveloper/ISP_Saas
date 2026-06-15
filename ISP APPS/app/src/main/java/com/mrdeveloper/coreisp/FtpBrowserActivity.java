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

public class FtpBrowserActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_ftp_browser);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        MaterialButton btnPlay = findViewById(R.id.btnPlayFeatured);
        MaterialButton btnDownload = findViewById(R.id.btnDownloadFeatured);

        btnPlay.setOnClickListener(v -> {
            // Mock streaming local FTP URL
            String videoUrl = "http://media.localisp.net/Movies/Hollywood/Dune_Part_Two.mp4";
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(videoUrl));
            intent.setDataAndType(Uri.parse(videoUrl), "video/mp4");
            startActivity(intent);
        });

        btnDownload.setOnClickListener(v -> {
            Toast.makeText(this, "Downloading from Local Server at 1 Gbps...", Toast.LENGTH_LONG).show();
            // Implement Android DownloadManager logic here
        });
        
        // In a real app, make API call to Laravel /api/customer/media-server
        // And populate the Horizontal RecyclerViews (Trending, Action, etc)
    }
}
