import React, { useState, useEffect } from "react";
import { LandingPage } from "./components/LandingPage";
import { MemberDashboard } from "./components/MemberDashboard";
import { AdminDashboard } from "./components/AdminDashboard";
import { Toaster } from "./components/ui/sonner";

export interface User {
  id: string;
  memberId?: string;
  name: string;
  email: string;
  mobile: string;
  address: string;
  role: "member" | "admin";
  joinDate: string;
  status: "active" | "inactive" | "pending";
}

export interface Contribution {
  id: string;
  memberId: string;
  amount: number;
  date: string;
  type: "monthly" | "additional";
  description: string;
}

export interface Loan {
  id: string;
  memberId: string;
  amount: number;
  termMonths: number;
  interestRate: number;
  status:
    | "pending"
    | "approved"
    | "active"
    | "completed"
    | "rejected";
  applicationDate: string;
  approvalDate?: string;
  startDate?: string;
  monthlyPayment: number;
  paymentsMade: number;
  totalPayments: number;
  nextPaymentDate?: string;
  agreementSigned: boolean;
}

export interface Notification {
  id: string;
  type:
    | "loan_application"
    | "profile_update"
    | "contribution"
    | "signup_request";
  memberId: string;
  memberName: string;
  message: string;
  date: string;
  status: "pending" | "approved" | "rejected";
  data?: any;
}

export interface AppState {
  currentUser: User | null;
  users: User[];
  contributions: Contribution[];
  loans: Loan[];
  notifications: Notification[];
  interestRate: number;
}

export default function App() {
  const [state, setState] = useState<AppState>({
    currentUser: null,
    users: [
      {
        id: "admin1",
        name: "System Administrator",
        email: "admin@loan.com",
        mobile: "+1234567890",
        address: "123 Admin St",
        role: "admin",
        joinDate: "2024-01-01",
        status: "active",
      },
      {
        id: "user1",
        memberId: "MBR-0001",
        name: "John Doe",
        email: "john@email.com",
        mobile: "+1234567891",
        address: "456 Member St",
        role: "member",
        joinDate: "2024-01-15",
        status: "active",
      },
      {
        id: "user2",
        memberId: "MBR-0002",
        name: "Jane Smith",
        email: "jane@email.com",
        mobile: "+1234567892",
        address: "789 Member Ave",
        role: "member",
        joinDate: "2024-02-01",
        status: "active",
      },
    ],
    contributions: [
      {
        id: "cont1",
        memberId: "MBR-0001",
        amount: 500,
        date: "2024-01-15",
        type: "monthly",
        description: "Monthly contribution",
      },
      {
        id: "cont2",
        memberId: "MBR-0001",
        amount: 200,
        date: "2024-01-20",
        type: "additional",
        description: "Additional savings",
      },
    ],
    loans: [
      {
        id: "loan1",
        memberId: "MBR-0001",
        amount: 10000,
        termMonths: 6,
        interestRate: 2,
        status: "active",
        applicationDate: "2024-01-20",
        approvalDate: "2024-01-22",
        startDate: "2024-02-01",
        monthlyPayment: 1700,
        paymentsMade: 4,
        totalPayments: 12,
        nextPaymentDate: "2024-04-15",
        agreementSigned: true,
      },
    ],
    notifications: [
      {
        id: "notif1",
        type: "loan_application",
        memberId: "MBR-0002",
        memberName: "Jane Smith",
        message: "New loan application for $15,000",
        date: "2024-03-15",
        status: "pending",
        data: {
          amount: 15000,
          termMonths: 12,
        },
      },
    ],
    interestRate: 2,
  });

  const login = (
    email: string,
    password: string,
    role: "member" | "admin",
  ) => {
    const user = state.users.find(
      (u) =>
        u.email === email &&
        u.role === role &&
        u.status === "active",
    );
    if (user) {
      setState((prev) => ({ ...prev, currentUser: user }));
      return true;
    }
    return false;
  };

  const signup = (userData: {
    name: string;
    email: string;
    mobile: string;
    address: string;
    password: string;
  }) => {
    // Check if email already exists
    const existingUser = state.users.find(
      (u) => u.email === userData.email,
    );
    if (existingUser) {
      return {
        success: false,
        message: "Email already exists",
      };
    }

    // Generate new member ID
    const memberNumbers = state.users
      .filter((u) => u.memberId)
      .map((u) => parseInt(u.memberId!.split("-")[1]))
      .filter((num) => !isNaN(num));

    const nextNumber =
      memberNumbers.length > 0
        ? Math.max(...memberNumbers) + 1
        : 1;
    const memberId = `MBR-${nextNumber.toString().padStart(4, "0")}`;

    // Create new user with pending status
    const newUser: User = {
      id: `user_${Date.now()}`,
      memberId,
      name: userData.name,
      email: userData.email,
      mobile: userData.mobile,
      address: userData.address,
      role: "member",
      joinDate: new Date().toISOString().split("T")[0],
      status: "pending",
    };

    // Create notification for admin
    const notification: Notification = {
      id: `notif_${Date.now()}`,
      type: "signup_request",
      memberId: memberId,
      memberName: userData.name,
      message: `New member signup request from ${userData.name}`,
      date: new Date().toISOString().split("T")[0],
      status: "pending",
      data: {
        email: userData.email,
        mobile: userData.mobile,
        address: userData.address,
        password: userData.password, // In real app, this should be hashed
      },
    };

    // Update state
    setState((prev) => ({
      ...prev,
      users: [...prev.users, newUser],
      notifications: [...prev.notifications, notification],
    }));

    return {
      success: true,
      message: `Signup request submitted successfully! Your Member ID is ${memberId}. Please wait for admin approval.`,
      memberId,
    };
  };

  const logout = () => {
    setState((prev) => ({ ...prev, currentUser: null }));
  };

  const updateState = (updates: Partial<AppState>) => {
    setState((prev) => ({ ...prev, ...updates }));
  };

  if (!state.currentUser) {
    return (
      <div className="min-h-screen bg-background">
        <LandingPage onLogin={login} onSignup={signup} />
        <Toaster />
      </div>
    );
  }

  if (state.currentUser.role === "admin") {
    return (
      <div className="min-h-screen bg-background">
        <AdminDashboard
          state={state}
          updateState={updateState}
          onLogout={logout}
        />
        <Toaster />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background">
      <MemberDashboard
        state={state}
        updateState={updateState}
        onLogout={logout}
      />
      <Toaster />
    </div>
  );
}