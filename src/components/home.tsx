"use client"

import Link from "next/link"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"

export default function Home() {
  return (
    <div className="min-h-screen">
      <header className="border-b bg-background">
        <div className="container mx-auto px-4 py-4">
          <div className="flex justify-between items-center">
            <Link
              href="/"
              className="text-2xl md:text-3xl font-bold text-primary hover:text-primary/90 transition-colors"
            >
              PositiveConnections
            </Link>
            <nav className="flex gap-4">
              <Button asChild variant="ghost">
                <Link href="/login">Login</Link>
              </Button>
              <Button asChild>
                <Link href="/register">Register</Link>
              </Button>
            </nav>
          </div>
        </div>
      </header>

      <main className="container mx-auto px-4 py-8 space-y-12">
        {/* Hero Section */}
        <section className="text-center max-w-3xl mx-auto space-y-6">
          <h1 className="text-4xl md:text-5xl font-bold">Find Your Soulmate</h1>
          <p className="text-xl text-muted-foreground">
            Connecting hearts, fostering understanding, and building lasting relationships.
          </p>
          <div className="flex gap-4 justify-center">
            <Button asChild size="lg" className="min-w-[120px]">
              <Link href="/register">Join Now</Link>
            </Button>
            <Button asChild variant="outline" size="lg" className="min-w-[120px]">
              <Link href="/login">Login</Link>
            </Button>
          </div>
        </section>

        {/* About Section */}
        <Card className="max-w-4xl mx-auto">
          <CardContent className="p-8 space-y-6">
            <h2 className="text-3xl font-bold">About GSNP+</h2>
            <div className="space-y-4 text-muted-foreground">
              <p>
                GSNP+ (Gujarat State Network of People living with HIV/AIDS) is a dedicated organization committed to helping
                individuals living with HIV/AIDS find companionship, love, and support. We believe that everyone
                deserves a chance at happiness and meaningful relationships, regardless of their HIV status.
              </p>
              <p>
                Our mission is to create a safe, judgment-free platform where people can connect, share experiences, and
                build lasting relationships. GSNP+ offers support, counseling, and resources to ensure that our
                community members have the best possible experience in their journey to find love and companionship.
              </p>
            </div>
            <Button asChild variant="link" className="p-0">
              <Link href="/about">Learn More About GSNP+</Link>
            </Button>
          </CardContent>
        </Card>

        {/* Features Section */}
        <section className="grid md:grid-cols-3 gap-6">
          <Card>
            <CardContent className="p-6 space-y-4">
              <h3 className="text-xl font-semibold">Safe Environment</h3>
              <p className="text-muted-foreground">
                Our platform prioritizes your privacy and safety, ensuring a secure space for meaningful connections.
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6 space-y-4">
              <h3 className="text-xl font-semibold">Supportive Community</h3>
              <p className="text-muted-foreground">
                Join a community that understands your journey and offers unwavering support and acceptance.
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6 space-y-4">
              <h3 className="text-xl font-semibold">Verified Profiles</h3>
              <p className="text-muted-foreground">
                We ensure all profiles are genuine, giving you peace of mind as you explore potential connections.
              </p>
            </CardContent>
          </Card>
        </section>
      </main>

      <footer className="bg-primary text-primary-foreground mt-12">
        <div className="container mx-auto px-4 py-6">
          <p className="text-center text-sm">© 2025 PositiveConnections. All rights reserved. Supported by GSNP+</p>
        </div>
      </footer>
    </div>
  )
}

