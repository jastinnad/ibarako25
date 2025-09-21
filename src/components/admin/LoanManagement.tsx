import React, { useState } from "react";
import { AppState, Loan } from "../../App";
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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "../ui/select";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "../ui/dialog";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "../ui/table";
import { Badge } from "../ui/badge";
import { Progress } from "../ui/progress";
import {
  Plus,
  CreditCard,
  CheckCircle,
  XCircle,
  DollarSign,
  Calendar,
  TrendingUp,
} from "lucide-react";
import { toast } from "sonner";

interface LoanManagementProps {
  state: AppState;
  updateState: (updates: Partial<AppState>) => void;
}

export function LoanManagement({ state, updateState }: LoanManagementProps) {
  const [createLoanOpen, setCreateLoanOpen] = useState(false);
  const [selectedMemberId, setSelectedMemberId] = useState("");
  const [newLoan, setNewLoan] = useState({
    amount: "",
    termMonths: "6",
  });

  const loans = state.loans;
  const members = state.users.filter((u) => u.role === "member");

  const pendingLoans = loans.filter((l) => l.status === "pending");
  const activeLoans = loans.filter((l) => l.status === "active");
  const completedLoans = loans.filter((l) => l.status === "completed");

  const totalLoanAmount = activeLoans.reduce(
    (sum, loan) => sum + loan.amount,
    0
  );

  const handleCreateLoan = () => {
    if (
      !selectedMemberId ||
      !newLoan.amount ||
      parseFloat(newLoan.amount) <= 0
    ) {
      toast.error("Please select a member and enter a valid amount");
      return;
    }

    const member = members.find((m) => m.memberId === selectedMemberId);
    if (!member) {
      toast.error("Member not found");
      return;
    }

    const termMonths = parseInt(newLoan.termMonths);
    const amount = parseFloat(newLoan.amount);
    const interestRate = state.interestRate;

    // Calculate payment details
    const totalPayments = termMonths * 2; // 2 payments per month
    const interestPerPayment = interestRate / 2 / 100; // Half of annual rate per payment
    const monthlyPayment = Math.round(
      amount / totalPayments + amount * interestPerPayment
    );

    const loan: Loan = {
      id: `loan_${Date.now()}`,
      memberId: selectedMemberId,
      amount,
      termMonths,
      interestRate,
      status: "approved",
      applicationDate: new Date().toISOString().split("T")[0],
      approvalDate: new Date().toISOString().split("T")[0],
      startDate: new Date().toISOString().split("T")[0],
      monthlyPayment,
      paymentsMade: 0,
      totalPayments,
      agreementSigned: true,
    };

    updateState({
      loans: [...state.loans, loan],
    });

    toast.success(
      `Loan of ${formatCurrency(amount)} created for member ${selectedMemberId}`
    );
    setCreateLoanOpen(false);
    setNewLoan({ amount: "", termMonths: "6" });
    setSelectedMemberId("");
  };

  const handleLoanAction = (loanId: string, action: "approve" | "reject") => {
    const updatedLoans = state.loans.map((loan) => {
      if (loan.id === loanId) {
        if (action === "approve") {
          return {
            ...loan,
            status: "approved" as const,
            approvalDate: new Date().toISOString().split("T")[0],
          };
        } else {
          return {
            ...loan,
            status: "rejected" as const,
          };
        }
      }
      return loan;
    });

    // Remove related notifications
    const updatedNotifications = state.notifications.filter(
      (n) => !(n.type === "loan_application" && n.data?.loanId === loanId)
    );

    updateState({
      loans: updatedLoans,
      notifications: updatedNotifications,
    });

    const loan = state.loans.find((l) => l.id === loanId);
    const member = members.find((m) => m.memberId === loan?.memberId);

    toast.success(`Loan ${action}d for ${member?.name}`);
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
    }).format(amount);
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
      approved: "secondary",
      active: "default",
      completed: "secondary",
      rejected: "destructive",
    };
    return (
      <Badge variant={variants[status] || "outline"}>
        {status.charAt(0).toUpperCase() + status.slice(1)}
      </Badge>
    );
  };

  const calculateProgress = (loan: Loan) => {
    return loan.totalPayments > 0
      ? (loan.paymentsMade / loan.totalPayments) * 100
      : 0;
  };

  const getMemberName = (memberId: string) => {
    return members.find((m) => m.memberId === memberId)?.name || "Unknown";
  };

  return (
    <div className="max-w-7xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-semibold">Loan Management</h2>
          <p className="text-muted-foreground">
            Manage member loans and applications
          </p>
        </div>
        <Dialog open={createLoanOpen} onOpenChange={setCreateLoanOpen}>
          <DialogTrigger asChild>
            <Button>
              <Plus className="w-4 h-4 mr-2" />
              Create Loan
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Create New Loan</DialogTitle>
              <DialogDescription>
                Create a loan directly for a member
              </DialogDescription>
            </DialogHeader>
            <div className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="member">Select Member</Label>
                <Select
                  value={selectedMemberId}
                  onValueChange={setSelectedMemberId}
                >
                  <SelectTrigger>
                    <SelectValue placeholder="Choose a member" />
                  </SelectTrigger>
                  <SelectContent>
                    {members.map((member) => (
                      <SelectItem key={member.id} value={member.memberId!}>
                        {member.memberId} - {member.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div className="space-y-2">
                <Label htmlFor="loan-amount">Loan Amount</Label>
                <Input
                  id="loan-amount"
                  type="number"
                  placeholder="Enter loan amount"
                  value={newLoan.amount}
                  onChange={(e) =>
                    setNewLoan((prev) => ({ ...prev, amount: e.target.value }))
                  }
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="loan-term">Loan Term</Label>
                <Select
                  value={newLoan.termMonths}
                  onValueChange={(value: any) =>
                    setNewLoan((prev) => ({ ...prev, termMonths: value }))
                  }
                >
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="3">3 Months</SelectItem>
                    <SelectItem value="6">6 Months</SelectItem>
                    <SelectItem value="12">12 Months</SelectItem>
                    <SelectItem value="18">18 Months</SelectItem>
                    <SelectItem value="24">24 Months</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div className="p-4 bg-blue-50 rounded-lg">
                <h4 className="font-medium mb-2">Loan Details</h4>
                <p className="text-sm text-gray-600 mb-1">
                  • Interest rate: {state.interestRate}% annually
                </p>
                <p className="text-sm text-gray-600 mb-1">
                  • Total payments: {parseInt(newLoan.termMonths) * 2}
                </p>
                <p className="text-sm text-gray-600">
                  • Payment dates: 15th and 30th of each month
                </p>
              </div>
              <div className="flex justify-end space-x-2">
                <Button
                  variant="outline"
                  onClick={() => setCreateLoanOpen(false)}
                >
                  Cancel
                </Button>
                <Button onClick={handleCreateLoan}>Create Loan</Button>
              </div>
            </div>
          </DialogContent>
        </Dialog>
      </div>

      {/* Summary Cards */}
      <div className="grid md:grid-cols-4 gap-6">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Pending Loans</CardTitle>
            <Calendar className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{pendingLoans.length}</div>
            <p className="text-xs text-muted-foreground">Awaiting approval</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Active Loans</CardTitle>
            <CreditCard className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{activeLoans.length}</div>
            <p className="text-xs text-muted-foreground">Currently active</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Amount</CardTitle>
            <DollarSign className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {formatCurrency(totalLoanAmount)}
            </div>
            <p className="text-xs text-muted-foreground">In active loans</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Completed</CardTitle>
            <TrendingUp className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{completedLoans.length}</div>
            <p className="text-xs text-muted-foreground">Fully repaid</p>
          </CardContent>
        </Card>
      </div>

      {/* Pending Loans */}
      {pendingLoans.length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle>Pending Loan Applications</CardTitle>
            <CardDescription>Loans awaiting your approval</CardDescription>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Member</TableHead>
                  <TableHead>Amount</TableHead>
                  <TableHead>Term</TableHead>
                  <TableHead>Application Date</TableHead>
                  <TableHead>Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {pendingLoans.map((loan) => (
                  <TableRow key={loan.id}>
                    <TableCell>
                      <div>
                        <div className="font-medium">
                          {getMemberName(loan.memberId)}
                        </div>
                        <div className="text-sm text-muted-foreground">
                          {loan.memberId}
                        </div>
                      </div>
                    </TableCell>
                    <TableCell className="font-medium">
                      {formatCurrency(loan.amount)}
                    </TableCell>
                    <TableCell>{loan.termMonths} months</TableCell>
                    <TableCell>{formatDate(loan.applicationDate)}</TableCell>
                    <TableCell>
                      <div className="flex space-x-2">
                        <Button
                          size="sm"
                          onClick={() => handleLoanAction(loan.id, "approve")}
                        >
                          <CheckCircle className="w-4 h-4 mr-1" />
                          Approve
                        </Button>
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => handleLoanAction(loan.id, "reject")}
                        >
                          <XCircle className="w-4 h-4 mr-1" />
                          Reject
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      )}

      {/* All Loans */}
      <Card>
        <CardHeader>
          <CardTitle>All Loans</CardTitle>
          <CardDescription>
            Complete history of all member loans
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Member</TableHead>
                <TableHead>Amount</TableHead>
                <TableHead>Term</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Progress</TableHead>
                <TableHead>Application Date</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loans
                .sort(
                  (a, b) =>
                    new Date(b.applicationDate).getTime() -
                    new Date(a.applicationDate).getTime()
                )
                .map((loan) => (
                  <TableRow key={loan.id}>
                    <TableCell>
                      <div>
                        <div className="font-medium">
                          {getMemberName(loan.memberId)}
                        </div>
                        <div className="text-sm text-muted-foreground">
                          {loan.memberId}
                        </div>
                      </div>
                    </TableCell>
                    <TableCell className="font-medium">
                      {formatCurrency(loan.amount)}
                    </TableCell>
                    <TableCell>{loan.termMonths} months</TableCell>
                    <TableCell>{getStatusBadge(loan.status)}</TableCell>
                    <TableCell>
                      {loan.status === "active" ? (
                        <div className="space-y-1">
                          <div className="flex justify-between text-sm">
                            <span>
                              {loan.paymentsMade}/{loan.totalPayments}
                            </span>
                            <span>{Math.round(calculateProgress(loan))}%</span>
                          </div>
                          <Progress
                            value={calculateProgress(loan)}
                            className="h-2"
                          />
                        </div>
                      ) : (
                        <span className="text-muted-foreground">-</span>
                      )}
                    </TableCell>
                    <TableCell>{formatDate(loan.applicationDate)}</TableCell>
                    <TableCell>
                      {loan.status === "pending" && (
                        <div className="flex space-x-2">
                          <Button
                            size="sm"
                            onClick={() => handleLoanAction(loan.id, "approve")}
                          >
                            <CheckCircle className="w-4 h-4" />
                          </Button>
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => handleLoanAction(loan.id, "reject")}
                          >
                            <XCircle className="w-4 h-4" />
                          </Button>
                        </div>
                      )}
                    </TableCell>
                  </TableRow>
                ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  );
}
