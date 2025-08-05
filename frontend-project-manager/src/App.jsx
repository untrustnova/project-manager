import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Layout from './components/Layout';
import Dashboard from './pages/Dashboard';
import EditProfile from './pages/EditProfile';
import Projects from './pages/Projects';
import NotFound from './pages/NotFound';
import Tasks from './pages/Tasks';
import Login from './pages/Login';
import Activity from './pages/Activity';
import ErrorBoundary from './components/ErrorBoundary';
import CreateEmployee from './pages/CreateEmployee';
import LeaveSubmission from './pages/LeaveSubmission';
import EditEmployee from './pages/EditEmployee';
import EmployeeList from './pages/EmployeeList';
import AuthorizationProvider from './layout/Authorization';
import AlertProvider from './layout/Alert'

function App() {
  return <AlertProvider>
    <BrowserRouter>
      <ErrorBoundary>
        <Routes>
          <Route path="/" element={<AuthorizationProvider />}>
            <Route path="/login" element={<Login />} />
            <Route path="/" element={<Layout />} >
              <Route index element={<Dashboard />} />
              <Route path="profile/edit" element={<EditProfile />} />
              <Route path="projects" element={<Projects />} />
              <Route path="tasks" element={<Tasks />} />
              <Route path="activity" element={<Activity />} />
              <Route path="employee" element={<EmployeeList />} />
              <Route path="employee/create" element={<CreateEmployee />} />
              <Route path="employee/:id/edit" element={<EditEmployee />} />
              <Route path="leave" element={<LeaveSubmission />} />
            </Route>
            <Route path="*" element={<NotFound />} />
          </Route>
        </Routes>
      </ErrorBoundary>
    </BrowserRouter>
  </AlertProvider>
}

export default App;
