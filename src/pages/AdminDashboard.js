import React, { useState } from 'react';
import { Container, Row, Col, Card, Table, Badge, Button, Form } from 'react-bootstrap';
import { Users, BookOpen, Crown, Shield, Trash2, Edit } from 'lucide-react';
import { useAppContext } from '../context/AppContext';
import { platformStats } from '../data/mockData';
import { Navigate } from 'react-router-dom';

const AdminDashboard = () => {
  const { user, usersList, adminUpdateUser, adminDeleteUser } = useAppContext();
  const [editingUser, setEditingUser] = useState(null);
  const [editRole, setEditRole] = useState('');
  const [editPremium, setEditPremium] = useState(false);

  if (!user || user.role !== 'admin') {
    return <Navigate to="/" />;
  }

  const startEditing = (u) => {
    setEditingUser(u);
    setEditRole(u.role);
    setEditPremium(u.isPremium);
  };

  const saveEdit = () => {
    adminUpdateUser(editingUser.email, { role: editRole, isPremium: editPremium });
    setEditingUser(null);
  };

  const statCards = [
    { title: 'Total Users', value: platformStats.totalUsers, icon: <Users size={24} className="text-primary" /> },
    { title: 'Total Authors', value: platformStats.totalAuthors, icon: <BookOpen size={24} className="text-info" /> },
    { title: 'Active Subs', value: platformStats.activeSubscriptions, icon: <Crown size={24} className="text-warning" /> },
    { title: 'Total Reads', value: platformStats.totalReads, icon: <BookOpen size={24} className="text-success" /> },
  ];

  return (
    <div className="min-vh-100 py-5 bg-light">
      <Container>
        <div className="d-flex align-items-center mb-4 gap-2">
          <Shield size={32} className="text-purple" />
          <h2 className="fw-bold mb-0">Admin Control Panel</h2>
        </div>

        <Row className="mb-5 g-4">
          {statCards.map((stat, idx) => (
            <Col md={3} key={idx}>
              <Card className="border-0 shadow-sm h-100">
                <Card.Body className="d-flex align-items-center justify-content-between p-4">
                  <div>
                    <h6 className="text-muted mb-2">{stat.title}</h6>
                    <h3 className="fw-bold mb-0">{stat.value}</h3>
                  </div>
                  <div className="bg-light p-3 rounded-circle">{stat.icon}</div>
                </Card.Body>
              </Card>
            </Col>
          ))}
        </Row>

        <Card className="border-0 shadow-sm">
          <Card.Header className="bg-white border-bottom-0 pt-4 pb-0 px-4">
            <h5 className="fw-bold">User Management</h5>
          </Card.Header>
          <Card.Body className="p-0">
            <Table responsive hover className="mb-0">
              <thead className="bg-light">
                <tr>
                  <th className="px-4 py-3">User</th>
                  <th className="py-3">Role</th>
                  <th className="py-3">Premium Status</th>
                  <th className="py-3">Joined Date</th>
                  <th className="px-4 py-3 text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                {usersList.map((u) => (
                  <tr key={u.id}>
                    <td className="px-4 py-3">
                      <div className="fw-bold">{u.name}</div>
                      <div className="text-muted small">{u.email}</div>
                    </td>
                    <td className="py-3">
                      {editingUser === u ? (
                        <Form.Select size="sm" value={editRole} onChange={(e) => setEditRole(e.target.value)}>
                          <option value="user">User</option>
                          <option value="author">Author</option>
                          <option value="admin">Admin</option>
                        </Form.Select>
                      ) : (
                        <Badge bg={u.role === 'admin' ? 'danger' : u.role === 'author' ? 'info' : 'secondary'}>
                          {u.role.toUpperCase()}
                        </Badge>
                      )}
                    </td>
                    <td className="py-3">
                      {editingUser === u ? (
                        <Form.Check type="checkbox" checked={editPremium} onChange={(e) => setEditPremium(e.target.checked)} label="Premium" />
                      ) : (
                        u.isPremium ? <Badge bg="warning" text="dark"><Crown size={12} className="me-1"/> Premium</Badge> : <Badge bg="light" text="dark">Free</Badge>
                      )}
                    </td>
                    <td className="py-3 text-muted">{u.joinDate}</td>
                    <td className="px-4 py-3 text-end">
                      {editingUser === u ? (
                        <div className="d-flex gap-2 justify-content-end">
                          <Button size="sm" variant="success" onClick={saveEdit}>Save</Button>
                          <Button size="sm" variant="light" onClick={() => setEditingUser(null)}>Cancel</Button>
                        </div>
                      ) : (
                        <div className="d-flex gap-2 justify-content-end">
                          <Button size="sm" variant="light" className="text-primary" onClick={() => startEditing(u)}>
                            <Edit size={16} />
                          </Button>
                          <Button size="sm" variant="light" className="text-danger" onClick={() => adminDeleteUser(u.email)}>
                            <Trash2 size={16} />
                          </Button>
                        </div>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </Table>
          </Card.Body>
        </Card>
      </Container>
    </div>
  );
};

export default AdminDashboard;
