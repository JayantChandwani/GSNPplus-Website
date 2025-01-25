"use client"

import React, { useState } from "react"
import { Button } from "./ui/button"
import { Input } from "./ui/input"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "./ui/card"
import { Label } from "./ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "./ui/select"
import { HeartIcon } from "lucide-react"
import Image from "next/image"
import { validateStep, validateFileUpload } from "../../validators"

const steps = [
  "Personal Info",
  "Family Info",
  "Health Details",
  "Business Info",
  "Property Details",
  "References",
  "Account Setup",
  "Document Upload",
]

export default function MatrimonyRegistration() {
  const [currentStep, setCurrentStep] = useState(0)
  const [formData, setFormData] = useState({
    first_name: "",
    middle_name: "",
    last_name: "",
    dob: "",
    height: "",
    weight: "",
    gender: "",
    marital_status: "",
    family_members: "",
    hiv_positive_members: "",
    hiv_detection: "",
    art_status: "",
    cd4_count: "",
    employment: "",
    income: "",
    property_type: "",
    property_value: "",
    ref_name: "",
    ref_contact: "",
    username: "",
    email: "",
    password: "",
    confirm_password: "",
    photo: null,
    hiv_report: null,
    address_proof: null,
    id_proof: null,
  })

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target
    setFormData((prev) => ({ ...prev, [name]: value }))
  }

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const { name, files } = e.target
      setFormData((prev) => ({ ...prev, [name]: files[0] }))
    }
  }

  const nextStep = () => {
    if (validateStep(currentStep + 1)) {
      setCurrentStep((prev) => Math.min(prev + 1, steps.length - 1))
    }
  }

  const prevStep = () => setCurrentStep((prev) => Math.max(prev - 1, 0))

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!validateFileUpload()) {
      return
    }
    const formDataToSend = new FormData()
    Object.keys(formData).forEach((key) => {
      if (formData[key as keyof typeof formData] !== null) {
        formDataToSend.append(key, formData[key as keyof typeof formData] as string | Blob)
      }
    })

    try {
      const response = await fetch('/register_new.php', {
        method: 'POST',
        body: formDataToSend,
      })

      if (!response.ok) {
        throw new Error('Registration failed')
      }

      const result = await response.json()
      if (result.success) {
        // Handle successful registration
      } else {
        // Handle registration error
      }
    } catch (error) {
      console.error('Error:', error)
    }
  }

  const renderStep = () => {
    switch (currentStep) {
      case 0:
        return (
          <>
            <div className="space-y-2">
              <Label htmlFor="first_name">First Name</Label>
              <Input
                id="first_name"
                name="first_name"
                value={formData.first_name}
                onChange={handleInputChange}
                required
              />
              <span id="error-first_name" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="middle_name">Middle Name</Label>
              <Input id="middle_name" name="middle_name" value={formData.middle_name} onChange={handleInputChange} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="last_name">Last Name</Label>
              <Input id="last_name" name="last_name" value={formData.last_name} onChange={handleInputChange} required />
              <span id="error-last_name" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="dob">Date of Birth</Label>
              <Input id="dob" name="dob" type="date" value={formData.dob} onChange={handleInputChange} required />
              <span id="error-dob" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="height">Height (in inches)</Label>
              <Input
                id="height"
                name="height"
                type="number"
                value={formData.height}
                onChange={handleInputChange}
                required
              />
              <span id="error-height" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="weight">Weight (in kg)</Label>
              <Input
                id="weight"
                name="weight"
                type="number"
                value={formData.weight}
                onChange={handleInputChange}
                required
              />
              <span id="error-weight" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="gender">Gender</Label>
              <Select
                name="gender"
                value={formData.gender}
                onValueChange={(value) => handleInputChange({ target: { name: "gender", value } } as any)}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select gender" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="male">Male</SelectItem>
                  <SelectItem value="female">Female</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="marital_status">Marital Status</Label>
              <Select
                name="marital_status"
                value={formData.marital_status}
                onValueChange={(value) => handleInputChange({ target: { name: "marital_status", value } } as any)}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select marital status" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Single">Single</SelectItem>
                  <SelectItem value="Married">Married</SelectItem>
                  <SelectItem value="Divorced">Divorced</SelectItem>
                  <SelectItem value="Widowed">Widowed</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </>
        )
      case 1:
        return (
          <>
            <div className="space-y-2">
              <Label htmlFor="family_members">Number of Family Members</Label>
              <Input
                id="family_members"
                name="family_members"
                type="number"
                value={formData.family_members}
                onChange={handleInputChange}
                required
              />
              <span id="error-family_members" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="hiv_positive_members">Number of HIV Positive Family Members</Label>
              <Input
                id="hiv_positive_members"
                name="hiv_positive_members"
                type="number"
                value={formData.hiv_positive_members}
                onChange={handleInputChange}
                required
              />
              <span id="error-hiv_positive_members" className="error text-red-500"></span>
            </div>
          </>
        )
      case 2:
        return (
          <>
            <div className="space-y-2">
              <Label htmlFor="hiv_detection">HIV Detection Date</Label>
              <Input
                id="hiv_detection"
                name="hiv_detection"
                type="date"
                value={formData.hiv_detection}
                onChange={handleInputChange}
                required
              />
              <span id="error-hiv_detection" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="art_status">ART Status</Label>
              <Select
                name="art_status"
                value={formData.art_status}
                onValueChange={(value) => handleInputChange({ target: { name: "art_status", value } } as any)}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select ART status" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Positive">Positive</SelectItem>
                  <SelectItem value="Negative">Negative</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="cd4_count">CD4 Count (per cubic mm)</Label>
              <Input
                id="cd4_count"
                name="cd4_count"
                type="number"
                value={formData.cd4_count}
                onChange={handleInputChange}
                required
              />
              <span id="error-cd4_count" className="error text-red-500"></span>
            </div>
          </>
        )
      case 3:
        return (
          <>
            <div className="space-y-2">
              <Label htmlFor="employment">Type of Employment</Label>
              <Select
                name="employment"
                value={formData.employment}
                onValueChange={(value) => handleInputChange({ target: { name: "employment", value } } as any)}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select employment type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Private Sector">Private Sector</SelectItem>
                  <SelectItem value="Public Sector">Public Sector</SelectItem>
                  <SelectItem value="Personal Business">Personal Business</SelectItem>
                  <SelectItem value="Unemployed">Unemployed</SelectItem>
                </SelectContent>
              </Select>
              <span id="error-employment" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="income">Annual Income</Label>
              <Input
                id="income"
                name="income"
                type="number"
                value={formData.income}
                onChange={handleInputChange}
                required
              />
              <span id="error-income" className="error text-red-500"></span>
            </div>
          </>
        )
      case 4:
        return (
          <>
            <div className="space-y-2">
              <Label htmlFor="property_type">Property Type</Label>
              <Select
                name="property_type"
                value={formData.property_type}
                onValueChange={(value) => handleInputChange({ target: { name: "property_type", value } } as any)}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select property type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Land">Land</SelectItem>
                  <SelectItem value="Flat">Flat</SelectItem>
                  <SelectItem value="Bungalow">Bungalow</SelectItem>
                  <SelectItem value="Others">Others</SelectItem>
                  <SelectItem value="-NA-">-NA-</SelectItem>
                </SelectContent>
              </Select>
              <span id="error-property_type" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="property_value">Property Value</Label>
              <Input
                id="property_value"
                name="property_value"
                type="number"
                value={formData.property_value}
                onChange={handleInputChange}
                required
              />
              <span id="error-property_value" className="error text-red-500"></span>
            </div>
          </>
        )
      case 5:
        return (
          <>
            <div className="space-y-2">
              <Label htmlFor="ref_name">Reference Name</Label>
              <Input
                id="ref_name"
                name="ref_name"
                value={formData.ref_name}
                onChange={handleInputChange}
                required
              />
              <span id="error-ref_name" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="ref_contact">Reference Contact</Label>
              <Input
                id="ref_contact"
                name="ref_contact"
                value={formData.ref_contact}
                onChange={handleInputChange}
                required
              />
              <span id="error-ref_contact" className="error text-red-500"></span>
            </div>
          </>
        )
      case 6:
        return (
          <>
            <div className="space-y-2">
              <Label htmlFor="username">Username</Label>
              <Input
                id="username"
                name="username"
                value={formData.username}
                onChange={handleInputChange}
                required
              />
              <span id="error-username" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                name="email"
                type="email"
                value={formData.email}
                onChange={handleInputChange}
                required
              />
              <span id="error-email" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="password">Password</Label>
              <Input
                id="password"
                name="password"
                type="password"
                value={formData.password}
                onChange={handleInputChange}
                required
              />
              <span id="error-password" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="confirm_password">Confirm Password</Label>
              <Input
                id="confirm_password"
                name="confirm_password"
                type="password"
                value={formData.confirm_password}
                onChange={handleInputChange}
                required
              />
              <span id="error-confirm_password" className="error text-red-500"></span>
            </div>
          </>
        )
      case 7:
        return (
          <>
            <div className="space-y-2">
              <Label htmlFor="photo">Photograph</Label>
              <Input id="photo" name="photo" type="file" onChange={handleFileChange} accept="image/*" required />
              <span id="error-photo" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="hiv_report">HIV Report</Label>
              <Input
                id="hiv_report"
                name="hiv_report"
                type="file"
                onChange={handleFileChange}
                accept="application/pdf,image/*"
                required
              />
              <span id="error-hiv_report" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="address_proof">Address Proof</Label>
              <Input
                id="address_proof"
                name="address_proof"
                type="file"
                onChange={handleFileChange}
                accept="application/pdf,image/*"
                required
              />
              <span id="error-address_proof" className="error text-red-500"></span>
            </div>
            <div className="space-y-2">
              <Label htmlFor="id_proof">ID Proof</Label>
              <Input
                id="id_proof"
                name="id_proof"
                type="file"
                onChange={handleFileChange}
                accept="application/pdf,image/*"
                required
              />
              <span id="error-id_proof" className="error text-red-500"></span>
            </div>
          </>
        )
      default:
        return <p>Step {currentStep + 1}</p>
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center p-4 relative">
      <Image
        src="/placeholder.svg?height=1080&width=1920"
        alt="Background Image"
        layout="fill"
        objectFit="cover"
        className="z-0"
      />
      <Card className="w-full max-w-2xl z-10 bg-white/90 backdrop-blur-sm">
        <CardHeader className="space-y-1">
          <CardTitle className="text-2xl font-bold text-center flex items-center justify-center">
            <HeartIcon className="mr-2 h-6 w-6 text-red-600" />
            Positive Connections Registration
          </CardTitle>
          <CardDescription className="text-center">
            Step {currentStep + 1} of {steps.length}: {steps[currentStep]}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form className="space-y-4" onSubmit={handleSubmit}>{renderStep()}</form>
        </CardContent>
        <CardFooter className="flex justify-between">
          <Button onClick={prevStep} disabled={currentStep === 0} variant="outline">
            Previous
          </Button>
          <Button onClick={currentStep === steps.length - 1 ? handleSubmit : nextStep}>
            {currentStep === steps.length - 1 ? "Submit" : "Next"}
          </Button>
        </CardFooter>
      </Card>
    </div>
  )
}
