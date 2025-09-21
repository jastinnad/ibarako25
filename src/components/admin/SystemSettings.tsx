import React, { useState } from "react";
import { AppState } from "../../App";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "../ui/card";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import { Label } from "../ui/label";
import { Separator } from "../ui/separator";
import { Badge } from "../ui/badge";
import {
  Settings,
  Percent,
  Calendar,
  Users,
  CreditCard,
  Save,
  AlertTriangle,
} from "lucide-react";
import { toast } from "sonner";

interface SystemSettingsProps {
  state: AppState;
  updateState: (updates: Partial<AppState>) => void;
}

export function SystemSettings({ state, updateState }: SystemSettingsProps) {
  const [interestRate, setInterestRate] = useState(
    state.interestRate.toString()
  );
  const [hasUnsavedChanges, setHasUnsavedChanges] = useState(false);

  const handleInterestRateChange = (value: string) => {
    setInterestRate(value);
    setHasUnsavedChanges(parseFloat(value) !== state.interestRate);
  };

  const handleSaveInterestRate = () => {
    const newRate = parseFloat(interestRate);
    if (isNaN(newRate) || newRate < 0 || newRate > 100) {
      toast.error("Please enter a valid interest rate between 0% and 100%");
      return;
    }

    updateState({ interestRate: newRate });
    setHasUnsavedChanges(false);
    toast.success(`Interest rate updated to ${newRate}%`);
  };

  const resetToDefault = () => {
    setInterestRate("2");
    setHasUnsavedChanges(true);
  };

  // System statistics
  const stats = {
    totalMembers: state.users.filter((u) => u.role === "member").length,
    activeMembers: state.users.filter(
      (u) => u.role === "member" && u.status === "active"
    ).length,
    totalLoans: state.loans.length,
    activeLoans: state.loans.filter((l) => l.status === "active").length,
    pendingLoans: state.loans.filter((l) => l.status === "pending").length,
    totalLoanAmount: state.loans
      .filter((l) => l.status === "active")
      .reduce((sum, loan) => sum + loan.amount, 0),
    totalContributions: state.contributions.reduce(
      (sum, cont) => sum + cont.amount,
      0
    ),
    pendingNotifications: state.notifications.filter(
      (n) => n.status === "pending"
    ).length,
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
    }).format(amount);
  };

  return (
    <div className="max-w-6xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-semibold">System Settings</h2>
          <p className="text-muted-foreground">
            Configure system parameters and view statistics
          </p>
        </div>
      </div>

      <div className="grid lg:grid-cols-2 gap-6">
        {/* Interest Rate Settings */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center space-x-2">
              <Percent className="w-5 h-5" />
              <span>Interest Rate Configuration</span>
            </CardTitle>
            <CardDescription>
              Set the annual interest rate for all loans
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="interest-rate">Annual Interest Rate (%)</Label>
              <Input
                id="interest-rate"
                type="number"
                step="0.1"
                min="0"
                max="100"
                value={interestRate}
                onChange={(e) => handleInterestRateChange(e.target.value)}
                placeholder="Enter interest rate"
              />
            </div>

            <div className="p-4 bg-blue-50 rounded-lg">
              <h4 className="font-medium mb-2">Payment Structure</h4>
              <div className="space-y-1 text-sm text-blue-800">
                <p>• Payments made twice monthly (15th and 30th)</p>
                <p>
                  • Interest split equally: {parseFloat(interestRate) / 2}% per
                  payment
                </p>
                <p>• Current rate: {state.interestRate}% annually</p>
              </div>
            </div>

            {hasUnsavedChanges && (
              <div className="p-3 bg-orange-50 border border-orange-200 rounded-lg">
                <div className="flex items-center space-x-2">
                  <AlertTriangle className="w-4 h-4 text-orange-600" />
                  <p className="text-sm text-orange-800">
                    You have unsaved changes
                  </p>
                </div>
              </div>
            )}

            <div className="flex space-x-2">
              <Button
                onClick={handleSaveInterestRate}
                disabled={!hasUnsavedChanges}
              >
                <Save className="w-4 h-4 mr-2" />
                Save Changes
              </Button>
              <Button variant="outline" onClick={resetToDefault}>
                Reset to Default (2%)
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* System Statistics */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center space-x-2">
              <Settings className="w-5 h-5" />
              <span>System Statistics</span>
            </CardTitle>
            <CardDescription>
              Overview of system usage and metrics
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-1">
                  <p className="text-sm text-muted-foreground">Total Members</p>
                  <p className="text-2xl font-bold">{stats.totalMembers}</p>
                </div>
                <div className="space-y-1">
                  <p className="text-sm text-muted-foreground">
                    Active Members
                  </p>
                  <p className="text-2xl font-bold text-green-600">
                    {stats.activeMembers}
                  </p>
                </div>
                <div className="space-y-1">
                  <p className="text-sm text-muted-foreground">Total Loans</p>
                  <p className="text-2xl font-bold">{stats.totalLoans}</p>
                </div>
                <div className="space-y-1">
                  <p className="text-sm text-muted-foreground">Active Loans</p>
                  <p className="text-2xl font-bold text-blue-600">
                    {stats.activeLoans}
                  </p>
                </div>
              </div>

              <Separator />

              <div className="space-y-3">
                <div className="flex justify-between items-center">
                  <span className="text-sm">Pending Loans</span>
                  <Badge variant="outline">{stats.pendingLoans}</Badge>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-sm">Pending Notifications</span>
                  <Badge variant="outline">{stats.pendingNotifications}</Badge>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-sm">Total Loan Amount</span>
                  <span className="font-medium">
                    {formatCurrency(stats.totalLoanAmount)}
                  </span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-sm">Total Contributions</span>
                  <span className="font-medium">
                    {formatCurrency(stats.totalContributions)}
                  </span>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Payment Schedule Information */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Calendar className="w-5 h-5" />
            <span>Payment Schedule Information</span>
          </CardTitle>
          <CardDescription>How the loan payment system works</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-2 gap-6">
            <div className="space-y-4">
              <h4 className="font-medium">Payment Frequency</h4>
              <div className="space-y-2 text-sm">
                <p>
                  • Payments are made <strong>twice monthly</strong>
                </p>
                <p>
                  • Payment dates: <strong>15th and 30th</strong> of each month
                </p>
                <p>
                  • For a 6-month loan = <strong>12 total payments</strong>
                </p>
                <p>• Progress shown as payments made / total payments</p>
              </div>
            </div>

            <div className="space-y-4">
              <h4 className="font-medium">Interest Calculation</h4>
              <div className="space-y-2 text-sm">
                <p>• Annual rate split into bi-monthly payments</p>
                <p>
                  • Current rate:{" "}
                  <strong>{state.interestRate}% annually</strong>
                </p>
                <p>
                  • Per payment: <strong>{state.interestRate / 2}%</strong> of
                  principal
                </p>
                <p>• Interest calculated on original loan amount</p>
              </div>
            </div>
          </div>

          <Separator className="my-4" />

          <div className="p-4 bg-gray-50 rounded-lg">
            <h4 className="font-medium mb-2">Example Calculation</h4>
            <div className="text-sm space-y-1">
              <p>
                Loan Amount: $10,000 | Term: 6 months | Rate:{" "}
                {state.interestRate}%
              </p>
              <p>Total Payments: 12 (twice monthly for 6 months)</p>
              <p>Principal per payment: $10,000 ÷ 12 = $833.33</p>
              <p>
                Interest per payment: $10,000 × {state.interestRate / 2}% = $
                {((10000 * state.interestRate) / 2 / 100).toFixed(2)}
              </p>
              <p>
                Total per payment: $833.33 + $
                {((10000 * state.interestRate) / 2 / 100).toFixed(2)} = $
                {(833.33 + (10000 * state.interestRate) / 2 / 100).toFixed(2)}
              </p>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Current Configuration Summary */}
      <Card>
        <CardHeader>
          <CardTitle>Current System Configuration</CardTitle>
          <CardDescription>Summary of current system settings</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-3 gap-4">
            <div className="space-y-2">
              <Label className="text-sm font-medium">Interest Rate</Label>
              <div className="text-lg font-bold">
                {state.interestRate}% annually
              </div>
              <p className="text-xs text-muted-foreground">
                {state.interestRate / 2}% per payment
              </p>
            </div>

            <div className="space-y-2">
              <Label className="text-sm font-medium">Payment Schedule</Label>
              <div className="text-lg font-bold">Bi-monthly</div>
              <p className="text-xs text-muted-foreground">
                15th and 30th of each month
              </p>
            </div>

            <div className="space-y-2">
              <Label className="text-sm font-medium">Member ID Format</Label>
              <div className="text-lg font-bold font-mono">MBR-0001</div>
              <p className="text-xs text-muted-foreground">
                Auto-incrementing format
              </p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
