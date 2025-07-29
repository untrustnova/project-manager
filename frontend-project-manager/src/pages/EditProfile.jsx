// src/pages/EditProfile.jsx
import { CalendarDays } from 'lucide-react';
import { useState } from 'react';

export default function EditProfile() {
  const [profile, setProfile] = useState({
    name: "Arizeta",
    email: "Freyacarol@email.com",
    password: "QQQ123",
    phone: "0867744666778",
    telegram: "FreyaaC",
    address: "Semarang, Central Java",
    birthDate: "13 February 2006",
    avatar: "https://i.pinimg.com/236x/6d/ff/11/6dff11a7f0c78e166d1c440c0ba2edca.jpg"
  });

  const [stats] = useState({
    projects: 10,
    tasksDone: 144,
    leaveDays: 4,
    workHoursPercentage: 70
  });

  const [isEditing, setIsEditing] = useState(false);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setProfile(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Here you would typically send the data to an API
    setIsEditing(false);
    console.log("Profile updated:", profile);
  };

  return (
    <div className="p-4 md:p-8">
      <div className="bg-white rounded-lg shadow p-4 md:p-6 flex flex-col md:flex-row gap-6 md:gap-8">
        {/* Left Column - Profile Summary */}
        <div className="w-full md:w-64 flex flex-col items-center text-center">
          <div className="relative">
            <img
              src={profile.avatar}
              className="rounded-full w-20 h-20 md:w-24 md:h-24 object-cover"
              alt="avatar"
            />
            {isEditing && (
              <button className="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-sky-400 hover:bg-sky-500 text-white text-xs px-3 py-1 rounded shadow">
                Change
              </button>
            )}
          </div>
          
          <h2 className="text-lg font-semibold mt-3">{profile.name}</h2>
          <p className="text-sm text-gray-500">Default Roles</p>

          <div className="mt-6 w-full space-y-2">
            <ProfileStat label="Project Total" value={stats.projects} />
            <ProfileStat label="Tasks Done" value={stats.tasksDone} />
            <ProfileStat label="Total Leave" value={stats.leaveDays} />
          </div>

          {/* Work Hours */}
          <div className="mt-4 w-full text-left">
            <p className="text-sm font-medium mb-1">Work hours</p>
            <div className="w-full bg-gray-200 rounded-full h-4">
              <div 
                className="bg-blue-900 h-4 rounded-full text-white text-xs flex items-center justify-center" 
                style={{ width: `${stats.workHoursPercentage}%` }}
              >
                {stats.workHoursPercentage}%
              </div>
            </div>
          </div>
        </div>

        {/* Right Column - Editable Fields */}
        <div className="flex-1">
          <form onSubmit={handleSubmit}>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <InputField 
                label="Name"
                name="name"
                value={profile.name}
                onChange={handleChange}
                disabled={!isEditing}
              />
              <InputField 
                label="Email" 
                name="email"
                value={profile.email}
                onChange={handleChange}
                disabled={!isEditing}
              />
              <InputField 
                label="Password" 
                name="password"
                type="password" 
                value={profile.password}
                onChange={handleChange}
                disabled={!isEditing}
              />
              <InputField 
                label="Phone Number" 
                name="phone"
                value={profile.phone}
                onChange={handleChange}
                disabled={!isEditing}
              />
              <InputField 
                label="Link Telegram" 
                name="telegram"
                value={profile.telegram}
                onChange={handleChange}
                disabled={!isEditing}
              />
              <InputField 
                label="Address" 
                name="address"
                value={profile.address}
                onChange={handleChange}
                disabled={!isEditing}
              />
              <InputField
                label="Birth"
                name="birthDate"
                value={profile.birthDate}
                onChange={handleChange}
                icon={<CalendarDays className="w-4 h-4 text-gray-400" />}
                disabled={!isEditing}
              />
            </div>

            <div className="flex justify-end gap-2 mt-6">
              {isEditing ? (
                <>
                  <button 
                    type="button"
                    className="px-5 py-2 border rounded hover:bg-gray-50"
                    onClick={() => setIsEditing(false)}
                  >
                    Cancel
                  </button>
                  <button 
                    type="submit"
                    className="bg-black text-white px-5 py-2 rounded hover:opacity-80"
                  >
                    Save Changes
                  </button>
                </>
              ) : (
                <button 
                  type="button"
                  className="bg-black text-white px-5 py-2 rounded hover:opacity-80"
                  onClick={() => setIsEditing(true)}
                >
                  Edit Profile
                </button>
              )}
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}

function InputField({ label, name, value, type = "text", icon, disabled = false, onChange }) {
  return (
    <div>
      <label className="text-sm font-medium text-gray-700">{label}</label>
      <div className="relative">
        <input
          name={name}
          type={type}
          value={value}
          onChange={onChange}
          disabled={disabled}
          className={`mt-1 w-full px-3 py-2 border rounded text-sm focus:outline-none focus:ring-2 ${disabled ? 'bg-gray-100' : 'focus:ring-blue-400'}`}
        />
        {icon && <div className="absolute right-3 top-1/2 transform -translate-y-1/2">{icon}</div>}
      </div>
    </div>
  );
}

function ProfileStat({ label, value }) {
  return (
    <div className="flex justify-between items-center text-sm">
      <span className="text-gray-600">{label}</span>
      <span className="bg-gray-100 px-2 py-1 rounded font-semibold">{value}</span>
    </div>
  );
}