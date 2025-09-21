import React, { useState } from "react";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "./ui/card";
import { Button } from "./ui/button";
import { Input } from "./ui/input";
import { Label } from "./ui/label";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "./ui/tabs";
import { Users, Shield, CreditCard, PiggyBank } from "lucide-react";
import { toast } from "sonner";

interface LandingPageProps {
  onLogin: (
    email: string,
    password: string,
    role: "member" | "admin"
  ) => boolean;
  onSignup: (userData: {
    name: string;
    email: string;
    mobile: string;
    address: string;
    password: string;
  }) => { success: boolean; message: string; memberId?: string };
}

export function LandingPage({ onLogin, onSignup }: LandingPageProps) {
  const [loginData, setLoginData] = useState({
    email: "",
    password: "",
    role: "member" as "member" | "admin",
  });

  const [signupData, setSignupData] = useState({
    name: "",
    email: "",
    mobile: "",
    address: "",
    password: "",
    confirmPassword: "",
  });

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();
    const success = onLogin(
      loginData.email,
      loginData.password,
      loginData.role
    );
    if (success) {
      toast.success("Login successful!");
    } else {
      toast.error("Invalid credentials. Please try again.");
    }
  };

  const handleSignup = (e: React.FormEvent) => {
    e.preventDefault();
    if (signupData.password !== signupData.confirmPassword) {
      toast.error("Passwords do not match");
      return;
    }

    const result = onSignup({
      name: signupData.name,
      email: signupData.email,
      mobile: signupData.mobile,
      address: signupData.address,
      password: signupData.password,
    });

    if (result.success) {
      toast.success(result.message);
      // Reset form on success
      setSignupData({
        name: "",
        email: "",
        mobile: "",
        address: "",
        password: "",
        confirmPassword: "",
      });
    } else {
      toast.error(result.message);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
      {/* Header */}
      <header className="bg-white shadow-sm">
        <div className="container mx-auto px-4 py-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <PiggyBank className="h-8 w-8 text-primary" />
              <h1 className="text-xl font-semibold">LoanSystem</h1>
            </div>
            <div className="text-sm text-muted-foreground">
              Secure Loan Management System
            </div>
          </div>
        </div>
      </header>

      <div className="container mx-auto px-4 py-12">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          {/* Left side - Features */}
          <div className="space-y-8">
            <div>
              <h2 className="text-3xl font-bold text-gray-900 mb-4">
                Manage Your Loans with Confidence
              </h2>
              <p className="text-lg text-gray-600 mb-8">
                A comprehensive loan management system designed for members and
                administrators.
              </p>
            </div>

            <div className="grid gap-6">
              <div className="flex items-start space-x-4">
                <div className="bg-blue-100 p-3 rounded-lg">
                  <Users className="h-6 w-6 text-blue-600" />
                </div>
                <div>
                  <h3 className="font-semibold mb-2">Member Portal</h3>
                  <p className="text-gray-600">
                    Apply for loans, track contributions, and manage your
                    profile with ease.
                  </p>
                </div>
              </div>

              <div className="flex items-start space-x-4">
                <div className="bg-green-100 p-3 rounded-lg">
                  <Shield className="h-6 w-6 text-green-600" />
                </div>
                <div>
                  <h3 className="font-semibold mb-2">Admin Management</h3>
                  <p className="text-gray-600">
                    Complete control over member accounts, loan approvals, and
                    system settings.
                  </p>
                </div>
              </div>

              <div className="flex items-start space-x-4">
                <div className="bg-purple-100 p-3 rounded-lg">
                  <CreditCard className="h-6 w-6 text-purple-600" />
                </div>
                <div>
                  <h3 className="font-semibold mb-2">Flexible Payments</h3>
                  <p className="text-gray-600">
                    Bi-monthly payment schedule with transparent interest
                    calculations.
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Right side - Login/Signup */}
          <div className="max-w-md mx-auto w-full">
            <Tabs defaultValue="login" className="w-full">
              <TabsList className="grid w-full grid-cols-2">
                <TabsTrigger value="login">Login</TabsTrigger>
                <TabsTrigger value="signup">Sign Up</TabsTrigger>
              </TabsList>

              <TabsContent value="login">
                <Card>
                  <CardHeader>
                    <CardTitle>Welcome Back</CardTitle>
                    <CardDescription>
                      Sign in to your account to continue
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <form onSubmit={handleLogin} className="space-y-4">
                      <div className="space-y-2">
                        <Label htmlFor="email">Email Address</Label>
                        <Input
                          id="email"
                          type="email"
                          placeholder="Enter your email"
                          value={loginData.email}
                          onChange={(e) =>
                            setLoginData((prev) => ({
                              ...prev,
                              email: e.target.value,
                            }))
                          }
                          required
                        />
                      </div>

                      <div className="space-y-2">
                        <Label htmlFor="password">Password</Label>
                        <Input
                          id="password"
                          type="password"
                          placeholder="Enter your password"
                          value={loginData.password}
                          onChange={(e) =>
                            setLoginData((prev) => ({
                              ...prev,
                              password: e.target.value,
                            }))
                          }
                          required
                        />
                      </div>

                      <div className="space-y-2">
                        <Label>Login as</Label>
                        <div className="flex space-x-4">
                          <label className="flex items-center space-x-2">
                            <input
                              type="radio"
                              name="role"
                              value="member"
                              checked={loginData.role === "member"}
                              onChange={(e) =>
                                setLoginData((prev) => ({
                                  ...prev,
                                  role: e.target.value as "member" | "admin",
                                }))
                              }
                            />
                            <span>Member</span>
                          </label>
                          <label className="flex items-center space-x-2">
                            <input
                              type="radio"
                              name="role"
                              value="admin"
                              checked={loginData.role === "admin"}
                              onChange={(e) =>
                                setLoginData((prev) => ({
                                  ...prev,
                                  role: e.target.value as "member" | "admin",
                                }))
                              }
                            />
                            <span>Admin</span>
                          </label>
                        </div>
                      </div>

                      <Button type="submit" className="w-full">
                        Sign In
                      </Button>
                    </form>

                    <div className="mt-4 p-3 bg-gray-50 rounded-lg">
                      <p className="text-sm text-gray-600 mb-2">
                        Demo Credentials:
                      </p>
                      <p className="text-xs text-gray-500">
                        <strong>Admin:</strong> admin@loan.com / password
                        <br />
                        <strong>Member:</strong> john@email.com / password
                      </p>
                    </div>
                  </CardContent>
                </Card>
              </TabsContent>

              <TabsContent value="signup">
                <Card>
                  <CardHeader>
                    <CardTitle>Create Account</CardTitle>
                    <CardDescription>Register as a new member</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <form onSubmit={handleSignup} className="space-y-4">
                      <div className="space-y-2">
                        <Label htmlFor="signup-name">Full Name</Label>
                        <Input
                          id="signup-name"
                          placeholder="Enter your full name"
                          value={signupData.name}
                          onChange={(e) =>
                            setSignupData((prev) => ({
                              ...prev,
                              name: e.target.value,
                            }))
                          }
                          required
                        />
                      </div>

                      <div className="space-y-2">
                        <Label htmlFor="signup-email">Email Address</Label>
                        <Input
                          id="signup-email"
                          type="email"
                          placeholder="Enter your email"
                          value={signupData.email}
                          onChange={(e) =>
                            setSignupData((prev) => ({
                              ...prev,
                              email: e.target.value,
                            }))
                          }
                          required
                        />
                      </div>

                      <div className="space-y-2">
                        <Label htmlFor="signup-mobile">Mobile Number</Label>
                        <Input
                          id="signup-mobile"
                          placeholder="Enter your mobile number"
                          value={signupData.mobile}
                          onChange={(e) =>
                            setSignupData((prev) => ({
                              ...prev,
                              mobile: e.target.value,
                            }))
                          }
                          required
                        />
                      </div>

                      <div className="space-y-2">
                        <Label htmlFor="signup-address">Address</Label>
                        <Input
                          id="signup-address"
                          placeholder="Enter your address"
                          value={signupData.address}
                          onChange={(e) =>
                            setSignupData((prev) => ({
                              ...prev,
                              address: e.target.value,
                            }))
                          }
                          required
                        />
                      </div>

                      <div className="space-y-2">
                        <Label htmlFor="signup-password">Password</Label>
                        <Input
                          id="signup-password"
                          type="password"
                          placeholder="Create a password"
                          value={signupData.password}
                          onChange={(e) =>
                            setSignupData((prev) => ({
                              ...prev,
                              password: e.target.value,
                            }))
                          }
                          required
                        />
                      </div>

                      <div className="space-y-2">
                        <Label htmlFor="signup-confirm">Confirm Password</Label>
                        <Input
                          id="signup-confirm"
                          type="password"
                          placeholder="Confirm your password"
                          value={signupData.confirmPassword}
                          onChange={(e) =>
                            setSignupData((prev) => ({
                              ...prev,
                              confirmPassword: e.target.value,
                            }))
                          }
                          required
                        />
                      </div>

                      <Button type="submit" className="w-full">
                        Create Account
                      </Button>
                    </form>
                  </CardContent>
                </Card>
              </TabsContent>
            </Tabs>
          </div>
        </div>
      </div>
    </div>
  );
}
