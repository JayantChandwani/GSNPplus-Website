import { useState } from "react"
import { useRouter } from "next/router"
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Button } from "@/components/ui/button"
import { Alert, AlertDescription } from "@/components/ui/alert"

interface EditProfileProps {
  initialUsername: string
  initialEmail: string
}

export function EditProfile({ initialUsername, initialEmail }: EditProfileProps) {
  const [username, setUsername] = useState(initialUsername)
  const [email, setEmail] = useState(initialEmail)
  const [usernameError, setUsernameError] = useState("")
  const [emailError, setEmailError] = useState("")
  const [submitError, setSubmitError] = useState("")
  const router = useRouter()

  const validateForm = () => {
    let isValid = true
    if (username.length < 3) {
      setUsernameError("Username must be at least 3 characters long")
      isValid = false
    } else {
      setUsernameError("")
    }
    if (!/\S+@\S+\.\S+/.test(email)) {
      setEmailError("Please enter a valid email address")
      isValid = false
    } else {
      setEmailError("")
    }
    return isValid
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!validateForm()) return

    const formData = new FormData()
    formData.append("username", username)
    formData.append("email", email)

    try {
      const response = await fetch("edit.php", {
        method: "POST",
        body: formData,
      })

      if (response.ok) {
        router.push("/profile")
      } else {
        const errorData = await response.text()
        setSubmitError(errorData || "An error occurred while updating your profile")
      }
    } catch (error) {
      setSubmitError("An error occurred while submitting the form")
    }
  }

  const checkUsername = async (username: string) => {
    try {
      const response = await fetch(`check_username.php?username=${encodeURIComponent(username)}`)
      const data = await response.text()
      if (data === "exists" && username !== initialUsername) {
        setUsernameError("Username already exists. Please choose another.")
      } else {
        setUsernameError("")
      }
    } catch (error) {
      console.error("Error checking username:", error)
    }
  }

  return (
    <Card className="w-full max-w-md mx-auto">
      <CardHeader>
        <CardTitle>Edit Profile</CardTitle>
      </CardHeader>
      <form onSubmit={handleSubmit}>
        <CardContent className="space-y-4">
          {submitError && (
            <Alert variant="destructive">
              <AlertDescription>{submitError}</AlertDescription>
            </Alert>
          )}
          <div className="space-y-2">
            <Label htmlFor="username">Username</Label>
            <Input
              id="username"
              value={username}
              onChange={(e) => {
                setUsername(e.target.value)
                checkUsername(e.target.value)
              }}
            />
            {usernameError && <p className="text-sm text-red-500">{usernameError}</p>}
          </div>
          <div className="space-y-2">
            <Label htmlFor="email">Email</Label>
            <Input id="email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
            {emailError && <p className="text-sm text-red-500">{emailError}</p>}
          </div>
        </CardContent>
        <CardFooter className="flex justify-between">
          <Button type="submit">Save Changes</Button>
          <Button variant="outline" onClick={() => router.push("/profile")}>
            Cancel
          </Button>
        </CardFooter>
      </form>
    </Card>
  )
}

