'use client'

import { supabase } from "@/lib/supabase"
import { useRouter } from "next/navigation" //next.jsの画面遷移

export default function Dashboard() {
    const router = useRouter()

    const handleLogout = async () => {
        const { error } = await supabase.auth.signOut()
        if (!error){
            router.push('/')
        }
    }
    return (
        <div>
            <p>こんにちは、ゲストさん</p>
            <button
             onClick={handleLogout}
             >ログアウト</button>
        </div>
    )
}