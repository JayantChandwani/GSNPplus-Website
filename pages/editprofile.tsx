import React from "react";
import { EditProfile } from "../src/components/editprofile";  // Adjust the path if needed
import "../app/globals.css";

export default function EditProfilePage() {
  return <EditProfile initialUsername="defaultUsername" initialEmail="defaultEmail@example.com" />;
}
