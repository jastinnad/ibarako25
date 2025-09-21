import React from "react";
import { AppState, Notification } from "../../App";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "../ui/card";
import { Button } from "../ui/button";
import { Badge } from "../ui/badge";
import {
  Bell,
  CheckCircle,
  XCircle,
  User,
  CreditCard,
  DollarSign,
  AlertCircle,
  UserPlus,
} from "lucide-react";
import { toast } from "sonner";

interface NotificationCenterProps {
  state: AppState;
  updateState: (updates: Partial<AppState>) => void;
}

export function NotificationCenter({
  state,
  updateState,
}: NotificationCenterProps) {
  const notifications = state.notifications;
  const pendingNotifications = notifications.filter(
    (n) => n.status === "pending"
  );
  const processedNotifications = notifications.filter(
    (n) => n.status !== "pending"
  );

  const handleNotificationAction = (
    notificationId: string,
    action: "approve" | "reject"
  ) => {
    const notification = notifications.find((n) => n.id === notificationId);
    if (!notification) return;

    let updatedUsers = [...state.users];
    let updatedLoans = [...state.loans];

    // Handle different notification types
    if (notification.type === "signup_request") {
      // Handle signup request
      updatedUsers = state.users.map((user) => {
        if (user.memberId === notification.memberId) {
          return {
            ...user,
            status:
              action === "approve"
                ? ("active" as const)
                : ("inactive" as const),
          };
        }
        return user;
      });
    } else if (notification.type === "profile_update" && action === "approve") {
      // Update user profile
      updatedUsers = state.users.map((user) => {
        if (user.memberId === notification.memberId) {
          return {
            ...user,
            mobile: notification.data.mobile,
            email: notification.data.email,
          };
        }
        return user;
      });
    } else if (notification.type === "loan_application") {
      // Handle loan application
      const loan = state.loans.find(
        (l) => l.memberId === notification.memberId && l.status === "pending"
      );
      if (loan) {
        updatedLoans = state.loans.map((l) => {
          if (l.id === loan.id) {
            if (action === "approve") {
              return {
                ...l,
                status: "approved" as const,
                approvalDate: new Date().toISOString().split("T")[0],
              };
            } else {
              return {
                ...l,
                status: "rejected" as const,
              };
            }
          }
          return l;
        });
      }
    }

    // Update notification status
    const updatedNotifications = notifications.map((n) => {
      if (n.id === notificationId) {
        return {
          ...n,
          status:
            action === "approve"
              ? ("approved" as const)
              : ("rejected" as const),
        };
      }
      return n;
    });

    updateState({
      users: updatedUsers,
      loans: updatedLoans,
      notifications: updatedNotifications,
    });

    const member = state.users.find(
      (u) => u.memberId === notification.memberId
    );
    const actionText =
      notification.type === "signup_request"
        ? action === "approve"
          ? "approved and activated"
          : "rejected"
        : `${action}d`;
    toast.success(
      `${notification.type.replace("_", " ")} ${actionText} for ${member?.name}`
    );
  };

  const getNotificationIcon = (type: string) => {
    switch (type) {
      case "loan_application":
        return <CreditCard className="w-5 h-5" />;
      case "profile_update":
        return <User className="w-5 h-5" />;
      case "contribution":
        return <DollarSign className="w-5 h-5" />;
      case "signup_request":
        return <UserPlus className="w-5 h-5" />;
      default:
        return <Bell className="w-5 h-5" />;
    }
  };

  const getNotificationColor = (type: string) => {
    switch (type) {
      case "loan_application":
        return "bg-blue-100 text-blue-600";
      case "profile_update":
        return "bg-green-100 text-green-600";
      case "contribution":
        return "bg-purple-100 text-purple-600";
      case "signup_request":
        return "bg-orange-100 text-orange-600";
      default:
        return "bg-gray-100 text-gray-600";
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  };

  const getStatusBadge = (status: string) => {
    const variants: Record<
      string,
      "default" | "secondary" | "destructive" | "outline"
    > = {
      pending: "outline",
      approved: "default",
      rejected: "destructive",
    };
    return (
      <Badge variant={variants[status] || "outline"}>
        {status.charAt(0).toUpperCase() + status.slice(1)}
      </Badge>
    );
  };

  return (
    <div className="max-w-6xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-semibold">Notification Center</h2>
          <p className="text-muted-foreground">
            Manage member requests and applications
          </p>
        </div>
        <div className="flex items-center space-x-2">
          <Badge variant="outline" className="px-3 py-1">
            {pendingNotifications.length} pending
          </Badge>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid md:grid-cols-5 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">
              Total Notifications
            </CardTitle>
            <Bell className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{notifications.length}</div>
            <p className="text-xs text-muted-foreground">All notifications</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Pending</CardTitle>
            <AlertCircle className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {pendingNotifications.length}
            </div>
            <p className="text-xs text-muted-foreground">Awaiting action</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">
              Signup Requests
            </CardTitle>
            <UserPlus className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {notifications.filter((n) => n.type === "signup_request").length}
            </div>
            <p className="text-xs text-muted-foreground">New members</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">
              Loan Applications
            </CardTitle>
            <CreditCard className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {
                notifications.filter((n) => n.type === "loan_application")
                  .length
              }
            </div>
            <p className="text-xs text-muted-foreground">Loan requests</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">
              Profile Updates
            </CardTitle>
            <User className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {notifications.filter((n) => n.type === "profile_update").length}
            </div>
            <p className="text-xs text-muted-foreground">Update requests</p>
          </CardContent>
        </Card>
      </div>

      {/* Pending Notifications */}
      {pendingNotifications.length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center space-x-2">
              <AlertCircle className="w-5 h-5" />
              <span>Pending Notifications</span>
            </CardTitle>
            <CardDescription>
              Notifications requiring your immediate attention
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            {pendingNotifications.map((notification) => (
              <div
                key={notification.id}
                className="flex items-center justify-between p-4 border rounded-lg"
              >
                <div className="flex items-start space-x-4">
                  <div
                    className={`p-2 rounded-lg ${getNotificationColor(
                      notification.type
                    )}`}
                  >
                    {getNotificationIcon(notification.type)}
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center space-x-2 mb-1">
                      <p className="font-medium">{notification.memberName}</p>
                      <Badge variant="outline" className="text-xs">
                        {notification.memberId}
                      </Badge>
                    </div>
                    <p className="text-sm text-gray-600 mb-2">
                      {notification.message}
                    </p>
                    {notification.data && (
                      <div className="text-xs text-muted-foreground">
                        {notification.type === "signup_request" && (
                          <div>
                            Email: {notification.data.email} | Mobile:{" "}
                            {notification.data.mobile}
                          </div>
                        )}
                        {notification.type === "profile_update" && (
                          <div>
                            New Mobile: {notification.data.mobile} | New Email:{" "}
                            {notification.data.email}
                          </div>
                        )}
                        {notification.type === "loan_application" && (
                          <div>
                            Amount: $
                            {notification.data.amount?.toLocaleString()} | Term:{" "}
                            {notification.data.termMonths} months
                          </div>
                        )}
                      </div>
                    )}
                    <p className="text-xs text-muted-foreground mt-1">
                      {formatDate(notification.date)}
                    </p>
                  </div>
                </div>
                <div className="flex space-x-2">
                  <Button
                    size="sm"
                    onClick={() =>
                      handleNotificationAction(notification.id, "approve")
                    }
                  >
                    <CheckCircle className="w-4 h-4 mr-1" />
                    Approve
                  </Button>
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() =>
                      handleNotificationAction(notification.id, "reject")
                    }
                  >
                    <XCircle className="w-4 h-4 mr-1" />
                    Reject
                  </Button>
                </div>
              </div>
            ))}
          </CardContent>
        </Card>
      )}

      {/* All Notifications History */}
      <Card>
        <CardHeader>
          <CardTitle>Notification History</CardTitle>
          <CardDescription>
            Complete history of all member notifications
          </CardDescription>
        </CardHeader>
        <CardContent>
          {notifications.length === 0 ? (
            <div className="text-center py-8">
              <Bell className="h-12 w-12 text-muted-foreground mx-auto mb-4" />
              <h3 className="text-lg font-medium mb-2">No notifications</h3>
              <p className="text-muted-foreground">
                All member notifications will appear here
              </p>
            </div>
          ) : (
            <div className="space-y-3">
              {notifications
                .sort(
                  (a, b) =>
                    new Date(b.date).getTime() - new Date(a.date).getTime()
                )
                .map((notification) => (
                  <div
                    key={notification.id}
                    className="flex items-center justify-between p-3 border rounded-lg"
                  >
                    <div className="flex items-start space-x-3">
                      <div
                        className={`p-2 rounded-lg ${getNotificationColor(
                          notification.type
                        )}`}
                      >
                        {getNotificationIcon(notification.type)}
                      </div>
                      <div className="flex-1">
                        <div className="flex items-center space-x-2 mb-1">
                          <p className="font-medium text-sm">
                            {notification.memberName}
                          </p>
                          <Badge variant="outline" className="text-xs">
                            {notification.memberId}
                          </Badge>
                          <Badge variant="outline" className="text-xs">
                            {notification.type.replace("_", " ")}
                          </Badge>
                        </div>
                        <p className="text-sm text-gray-600 mb-1">
                          {notification.message}
                        </p>
                        <p className="text-xs text-muted-foreground">
                          {formatDate(notification.date)}
                        </p>
                      </div>
                    </div>
                    <div className="flex items-center space-x-2">
                      {getStatusBadge(notification.status)}
                      {notification.status === "pending" && (
                        <div className="flex space-x-1">
                          <Button
                            size="sm"
                            variant="outline"
                            onClick={() =>
                              handleNotificationAction(
                                notification.id,
                                "approve"
                              )
                            }
                          >
                            <CheckCircle className="w-3 h-3" />
                          </Button>
                          <Button
                            size="sm"
                            variant="outline"
                            onClick={() =>
                              handleNotificationAction(
                                notification.id,
                                "reject"
                              )
                            }
                          >
                            <XCircle className="w-3 h-3" />
                          </Button>
                        </div>
                      )}
                    </div>
                  </div>
                ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
