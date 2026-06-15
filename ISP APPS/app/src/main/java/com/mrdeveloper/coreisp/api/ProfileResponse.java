package com.mrdeveloper.coreisp.api;

public class ProfileResponse {
    private String name;
    private String speed;
    private String expiry_date;
    private String status;
    private int remaining_days;

    public String getName() { return name; }
    public String getSpeed() { return speed; }
    public String getExpiryDate() { return expiry_date; }
    public String getStatus() { return status; }
    public int getRemainingDays() { return remaining_days; }
}
