import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"

interface UserProfileProps {
  username: string
  email: string
  status: string
  userType: string
}

export function Profile({ username, email, status, userType }: UserProfileProps) {
  return (
    <Card className="w-full max-w-3xl mx-auto">
      <CardHeader>
        <div className="flex items-center space-x-4">
          <Avatar className="w-20 h-20">
            <AvatarImage src={`https://api.dicebear.com/6.x/initials/svg?seed=${username}`} alt={username} />
            <AvatarFallback>{username.slice(0, 2).toUpperCase()}</AvatarFallback>
          </Avatar>
          <div>
            <CardTitle className="text-2xl">{username}</CardTitle>
            <CardDescription>{email}</CardDescription>
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <div className="grid grid-cols-2 gap-4">
          <div>
            <h3 className="font-semibold text-sm text-muted-foreground mb-1">Status</h3>
            <Badge variant={status === "Active" ? "default" : "secondary"}>{status}</Badge>
          </div>
          <div>
            <h3 className="font-semibold text-sm text-muted-foreground mb-1">User Type</h3>
            <Badge variant="outline">{userType}</Badge>
          </div>
        </div>
      </CardContent>
      <CardFooter className="flex justify-between">
        <Button asChild variant="outline">
          <a href="edit.php">Edit Profile</a>
        </Button>
        <Button asChild variant="destructive">
          <a href="logout.php">Logout</a>
        </Button>
      </CardFooter>
    </Card>
  )
}

