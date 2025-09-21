import React, { useState } from 'react';
import { User, AppState } from '../../App';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
import { Badge } from '../ui/badge';
import { Edit, Calendar, Phone, Mail, MapPin, CreditCard } from 'lucide-react';
import { toast } from 'sonner@2.0.3';

interface ProfileViewProps {
  member: User;
  state: AppState;
  updateState: (updates: Partial<AppState>) => void;
}

export function ProfileView({ member, state, updateState }: ProfileViewProps) {
  const [updateRequestOpen, setUpdateRequestOpen] = useState(false);
  const [updateData, setUpdateData] = useState({
    mobile: member.mobile,
    email: member.email
  });

  const handleUpdateRequest = () => {
    const newNotification = {
      id: `notif_${Date.now()}`,
      type: 'profile_update' as const,
      memberId: member.memberId!,
      memberName: member.name,
      message: `Profile update request - Mobile: ${updateData.mobile}, Email: ${updateData.email}`,
      date: new Date().toISOString().split('T')[0],
      status: 'pending' as const,
      data: updateData
    };

    updateState({
      notifications: [...state.notifications, newNotification]
    });

    toast.success('Update request submitted successfully');
    setUpdateRequestOpen(false);
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      {/* Profile Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-semibold">Profile Information</h2>
          <p className="text-muted-foreground">View your personal details</p>
        </div>
        <Dialog open={updateRequestOpen} onOpenChange={setUpdateRequestOpen}>
          <DialogTrigger asChild>
            <Button variant="outline">
              <Edit className="w-4 h-4 mr-2" />
              Request Update
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Request Profile Update</DialogTitle>
              <DialogDescription>
                You can only request updates to your mobile number and email address. Other changes require admin approval.
              </DialogDescription>
            </DialogHeader>
            <div className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="update-mobile">Mobile Number</Label>
                <Input
                  id="update-mobile"
                  value={updateData.mobile}
                  onChange={(e) => setUpdateData(prev => ({ ...prev, mobile: e.target.value }))}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="update-email">Email Address</Label>
                <Input
                  id="update-email"
                  type="email"
                  value={updateData.email}
                  onChange={(e) => setUpdateData(prev => ({ ...prev, email: e.target.value }))}
                />
              </div>
              <div className="flex justify-end space-x-2">
                <Button variant="outline" onClick={() => setUpdateRequestOpen(false)}>
                  Cancel
                </Button>
                <Button onClick={handleUpdateRequest}>
                  Submit Request
                </Button>
              </div>
            </div>
          </DialogContent>
        </Dialog>
      </div>

      {/* Profile Details Card */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <CreditCard className="w-5 h-5" />
            <span>Member Information</span>
          </CardTitle>
          <CardDescription>
            Your profile information is managed by the system administrator
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-6">
          <div className="grid md:grid-cols-2 gap-6">
            <div className="space-y-4">
              <div>
                <Label className="text-sm font-medium text-muted-foreground">Member ID</Label>
                <div className="flex items-center space-x-2 mt-1">
                  <Badge variant="secondary" className="font-mono">
                    {member.memberId}
                  </Badge>
                </div>
              </div>

              <div>
                <Label className="text-sm font-medium text-muted-foreground">Full Name</Label>
                <Input value={member.name} disabled className="mt-1" />
              </div>

              <div>
                <Label className="text-sm font-medium text-muted-foreground">Email Address</Label>
                <div className="flex items-center space-x-2 mt-1">
                  <Mail className="w-4 h-4 text-muted-foreground" />
                  <Input value={member.email} disabled />
                </div>
              </div>

              <div>
                <Label className="text-sm font-medium text-muted-foreground">Mobile Number</Label>
                <div className="flex items-center space-x-2 mt-1">
                  <Phone className="w-4 h-4 text-muted-foreground" />
                  <Input value={member.mobile} disabled />
                </div>
              </div>
            </div>

            <div className="space-y-4">
              <div>
                <Label className="text-sm font-medium text-muted-foreground">Address</Label>
                <div className="flex items-center space-x-2 mt-1">
                  <MapPin className="w-4 h-4 text-muted-foreground" />
                  <Input value={member.address} disabled />
                </div>
              </div>

              <div>
                <Label className="text-sm font-medium text-muted-foreground">Join Date</Label>
                <div className="flex items-center space-x-2 mt-1">
                  <Calendar className="w-4 h-4 text-muted-foreground" />
                  <Input value={formatDate(member.joinDate)} disabled />
                </div>
              </div>

              <div>
                <Label className="text-sm font-medium text-muted-foreground">Account Status</Label>
                <div className="mt-1">
                  <Badge variant={member.status === 'active' ? 'default' : 'secondary'}>
                    {member.status.charAt(0).toUpperCase() + member.status.slice(1)}
                  </Badge>
                </div>
              </div>

              <div>
                <Label className="text-sm font-medium text-muted-foreground">Role</Label>
                <div className="mt-1">
                  <Badge variant="outline">
                    {member.role.charAt(0).toUpperCase() + member.role.slice(1)}
                  </Badge>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Information Notice */}
      <Card className="border-blue-200 bg-blue-50">
        <CardContent className="pt-6">
          <div className="flex items-start space-x-3">
            <div className="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
            <div>
              <p className="text-sm text-blue-800">
                <strong>Update Requests:</strong> You can request updates to your mobile number and email address using the "Request Update" button. 
                All other profile changes must be made by the system administrator.
              </p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}