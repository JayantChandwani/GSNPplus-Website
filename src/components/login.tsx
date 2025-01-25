"use client"

import React, { useState } from "react"
import { useRouter } from "next/router"
import { Button } from "./ui/button"
import { Input } from "./ui/input"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "./ui/card"
import { Label } from "./ui/label"
import { HeartIcon } from "lucide-react"
import Image from "next/image"

export default function MatrimonyLogin() {
  const [username, setUsername] = useState("")
  const [password, setPassword] = useState("")
  const [error, setError] = useState("")
  const router = useRouter()

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError("")

    if (!username || !password) {
      setError("Please fill in all fields")
      return
    }
    const loginData = {
      username: username,
      password: password,
    };
    try {
      const response = await fetch("http://localhost:80/frontend/login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(loginData),
      });
      console.log(response);
      const result = await response.json();
      console.log(result);
      if (result.success) {
        // Proceed with login success (redirect or change UI)
        console.log("Login successful");
      } else {
        // Show error message
        setError(result.message);
      }
    } catch (error) {
      console.error("Error:", error);
      setError("An error occurred. Please try again.");
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center p-4 relative bg-background text-foreground">
      <Image
        src="/placeholder.svg?height=1080&width=1920"
        alt="Background Image"
        layout="fill"
        objectFit="cover"
        className="z-0"
      />
      <Card className="w-full max-w-md z-10 bg-white/90 backdrop-blur-sm">
        <CardHeader className="space-y-1">
          <CardTitle className="text-2xl font-bold text-center flex items-center justify-center">
            <HeartIcon className="mr-2 h-6 w-6 text-red-600" />
            Positive Connections
          </CardTitle>
          <CardDescription className="text-center">
            A safe space for HIV+ individuals to find love and companionship
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit}>
            <div className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="username">Username</Label>
                <Input
                  id="username"
                  placeholder="Enter your username"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  required
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="password">Password</Label>
                <Input
                  id="password"
                  type="password"
                  placeholder="Enter your password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                />
              </div>
            </div>
            {error && <p className="text-sm text-red-500 mt-2">{error}</p>}
            <Button className="w-full mt-4 bg-primary hover:bg-primary/90 text-primary-foreground" type="submit">
              Login
            </Button>
          </form>
        </CardContent>
        <CardFooter className="flex flex-col items-center space-y-2">
          <p className="text-sm text-muted-foreground">
            Don't have an account?{" "}
            <a href="/register" className="text-primary hover:underline">
              Sign up
            </a>
          </p>
          <a href="/about" className="text-sm text-primary hover:underline">
            Learn more about our community
          </a>
        </CardFooter>
      </Card>
    </div>
  )
}