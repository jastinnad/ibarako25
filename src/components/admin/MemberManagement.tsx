import React, { useState } from 'react';
import { AppState, User } from '../../App';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '../ui/table';
import { Badge } from '../ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '../ui/select';
import { Plus, Users, UserPlus, Edit, Trash2, Eye } from 'lucide-react';
import { toast } from 'sonner@2.0.3';

interface MemberManagementProps {
  state: AppState;
  updateState: (updates: Partial<AppState>) => void;
}

export function MemberManagement({ state, updateState }: MemberManagementProps) {
  const [addMemberOpen, setAddMemberOpen] = useState(false);
  const [editMemberOpen, setEditMemberOpen] = useState(false);
  const [selectedMember, setSelectedMember] = useState<User | null>(null);
  const [newMember, setNewMember] = useState({
    name: '',
    email: '',
    mobile: '',
    address: '',
    status: 'active' as 'active' | 'inactive'
  });

  const members = state.users.filter(user => user.role === 'member');
  const activeMembers = members.filter(m => m.status === 'active').length;
  const inactiveMembers = members.filter(m => m.status === 'inactive').length;

  const generateMemberId = () => {
    const existingIds = members
      .map(m => m.memberId)
      .filter(id => id?.startsWith('MBR-'))
      .map(id => parseInt(id!.split('-')[1]))
      .sort((a, b) => b - a);
    
    const nextNumber = existingIds.length > 0 ? existingIds[0] + 1 : 1;
    return `MBR-${nextNumber.toString().padStart(4, '0')}`;
  };

  const handleAddMember = () => {
    if (!newMember.name || !newMember.email || !newMember.mobile || !newMember.address) {
      toast.error('Please fill in all fields');
      return;
    }

    // Check if email already exists
    if (state.users.some(u => u.email === newMember.email)) {
      toast.error('Email already exists');
      return;
    }

    const member: User = {
      id: `user_${Date.now()}`,
      memberId: generateMemberId(),
      name: newMember.name,
      email: newMember.email,
      mobile: newMember.mobile,
      address: newMember.address,
      role: 'member',
      joinDate: new Date().toISOString().split('T')[0],
      status: newMember.status
    };

    updateState({
      users: [...state.users, member]
    });

    toast.success(`Member ${member.memberId} added successfully`);
    setAddMemberOpen(false);
    setNewMember({ name: '', email: '', mobile: '', address: '', status: 'active' });
  };

  const handleEditMember = () => {
    if (!selectedMember) return;

    const updatedUsers = state.users.map(user =>
      user.id === selectedMember.id ? selectedMember : user
    );

    updateState({ users: updatedUsers });
    toast.success('Member updated successfully');
    setEditMemberOpen(false);
    setSelectedMember(null);
  };

  const handleDeleteMember = (member: User) => {
    if (confirm(`Are you sure you want to delete member ${member.memberId}?`)) {
      // Remove member and their data
      const updatedUsers = state.users.filter(u => u.id !== member.id);
      const updatedContributions = state.contributions.filter(c => c.memberId !== member.memberId);
      const updatedLoans = state.loans.filter(l => l.memberId !== member.memberId);
      const updatedNotifications = state.notifications.filter(n => n.memberId !== member.memberId);

      updateState({
        users: updatedUsers,
        contributions: updatedContributions,
        loans: updatedLoans,
        notifications: updatedNotifications
      });

      toast.success('Member deleted successfully');
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  const getMemberLoans = (memberId: string) => {
    return state.loans.filter(l => l.memberId === memberId);
  };

  const getMemberContributions = (memberId: string) => {
    return state.contributions.filter(c => c.memberId === memberId);
  };

  return (
    <div className="max-w-7xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-semibold">Member Management</h2>
          <p className="text-muted-foreground">Manage member accounts and information</p>
        </div>
        <Dialog open={addMemberOpen} onOpenChange={setAddMemberOpen}>
          <DialogTrigger asChild>
            <Button>
              <Plus className="w-4 h-4 mr-2" />
              Add Member
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Add New Member</DialogTitle>
              <DialogDescription>
                Create a new member account with a unique member ID
              </DialogDescription>
            </DialogHeader>
            <div className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="name">Full Name</Label>
                <Input
                  id="name"
                  placeholder="Enter full name"
                  value={newMember.name}
                  onChange={(e) => setNewMember(prev => ({ ...prev, name: e.target.value }))}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="email">Email Address</Label>
                <Input
                  id="email"
                  type="email"
                  placeholder="Enter email address"
                  value={newMember.email}
                  onChange={(e) => setNewMember(prev => ({ ...prev, email: e.target.value }))}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="mobile">Mobile Number</Label>
                <Input
                  id="mobile"
                  placeholder="Enter mobile number"
                  value={newMember.mobile}
                  onChange={(e) => setNewMember(prev => ({ ...prev, mobile: e.target.value }))}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="address">Address</Label>
                <Input
                  id="address"
                  placeholder="Enter address"
                  value={newMember.address}
                  onChange={(e) => setNewMember(prev => ({ ...prev, address: e.target.value }))}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="status">Status</Label>
                <Select
                  value={newMember.status}
                  onValueChange={(value: 'active' | 'inactive') => 
                    setNewMember(prev => ({ ...prev, status: value }))
                  }
                >
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="active">Active</SelectItem>
                    <SelectItem value="inactive">Inactive</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div className="p-3 bg-blue-50 rounded-lg">
                <p className="text-sm text-blue-800">
                  <strong>Member ID:</strong> {generateMemberId()}
                </p>
              </div>
              <div className="flex justify-end space-x-2">
                <Button variant="outline" onClick={() => setAddMemberOpen(false)}>
                  Cancel
                </Button>
                <Button onClick={handleAddMember}>
                  Add Member
                </Button>
              </div>
            </div>
          </DialogContent>
        </Dialog>
      </div>

      {/* Summary Cards */}
      <div className="grid md:grid-cols-3 gap-6">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Members</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{members.length}</div>
            <p className="text-xs text-muted-foreground">
              Registered members
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Active Members</CardTitle>
            <UserPlus className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{activeMembers}</div>
            <p className="text-xs text-muted-foreground">
              Active accounts
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Inactive Members</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{inactiveMembers}</div>
            <p className="text-xs text-muted-foreground">
              Inactive accounts
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Members Table */}
      <Card>
        <CardHeader>
          <CardTitle>Members List</CardTitle>
          <CardDescription>
            All registered members and their account information
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Member ID</TableHead>
                <TableHead>Name</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Mobile</TableHead>
                <TableHead>Join Date</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Loans</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {members.map((member) => {
                const memberLoans = getMemberLoans(member.memberId!);
                const memberContributions = getMemberContributions(member.memberId!);
                
                return (
                  <TableRow key={member.id}>
                    <TableCell className="font-mono">{member.memberId}</TableCell>
                    <TableCell className="font-medium">{member.name}</TableCell>
                    <TableCell>{member.email}</TableCell>
                    <TableCell>{member.mobile}</TableCell>
                    <TableCell>{formatDate(member.joinDate)}</TableCell>
                    <TableCell>
                      <Badge variant={member.status === 'active' ? 'default' : 'secondary'}>
                        {member.status}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <div className="text-sm">
                        <div>{memberLoans.length} loans</div>
                        <div className="text-muted-foreground">
                          {memberContributions.length} contributions
                        </div>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="flex space-x-2">
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => {
                            setSelectedMember(member);
                            setEditMemberOpen(true);
                          }}
                        >
                          <Edit className="w-4 h-4" />
                        </Button>
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => handleDeleteMember(member)}
                        >
                          <Trash2 className="w-4 h-4" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                );
              })}
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      {/* Edit Member Dialog */}
      <Dialog open={editMemberOpen} onOpenChange={setEditMemberOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Edit Member</DialogTitle>
            <DialogDescription>
              Update member information
            </DialogDescription>
          </DialogHeader>
          {selectedMember && (
            <div className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="edit-name">Full Name</Label>
                <Input
                  id="edit-name"
                  value={selectedMember.name}
                  onChange={(e) => setSelectedMember(prev => prev ? { ...prev, name: e.target.value } : null)}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="edit-email">Email Address</Label>
                <Input
                  id="edit-email"
                  type="email"
                  value={selectedMember.email}
                  onChange={(e) => setSelectedMember(prev => prev ? { ...prev, email: e.target.value } : null)}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="edit-mobile">Mobile Number</Label>
                <Input
                  id="edit-mobile"
                  value={selectedMember.mobile}
                  onChange={(e) => setSelectedMember(prev => prev ? { ...prev, mobile: e.target.value } : null)}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="edit-address">Address</Label>
                <Input
                  id="edit-address"
                  value={selectedMember.address}
                  onChange={(e) => setSelectedMember(prev => prev ? { ...prev, address: e.target.value } : null)}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="edit-status">Status</Label>
                <Select
                  value={selectedMember.status}
                  onValueChange={(value: 'active' | 'inactive') => 
                    setSelectedMember(prev => prev ? { ...prev, status: value } : null)
                  }
                >
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="active">Active</SelectItem>
                    <SelectItem value="inactive">Inactive</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div className="flex justify-end space-x-2">
                <Button variant="outline" onClick={() => setEditMemberOpen(false)}>
                  Cancel
                </Button>
                <Button onClick={handleEditMember}>
                  Update Member
                </Button>
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}