package com.mrdeveloper.coreisp.api;

import java.util.HashMap;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.POST;

public interface ApiService {

    @POST("login")
    Call<LoginResponse> login(@Body HashMap<String, String> request);

    @GET("customer/profile")
    Call<ProfileResponse> getProfile(@Header("Authorization") String token);

    @POST("customer/ticket")
    Call<GeneralResponse> submitTicket(
            @Header("Authorization") String token,
            @Body HashMap<String, String> request
    );

    @POST("customer/router-config")
    Call<GeneralResponse> updateRouterConfig(
            @Header("Authorization") String token,
            @Body HashMap<String, String> request
    );

    @GET("customer/usage")
    Call<Object> getUsageHistory(@Header("Authorization") String token);

    @GET("customer/billing-history")
    Call<Object> getBillingHistory(@Header("Authorization") String token);
}
