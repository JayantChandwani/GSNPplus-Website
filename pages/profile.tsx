import React from "react";
import { Profile } from "../src/components/profile";
import '../app/globals.css';

export default function ProfilePage() {
  const userProfile = {
    username: "exampleUser",
    email: "user@example.com",
    status: "active",
    userType: "admin"
  };

  return <Profile {...userProfile} />;
}
