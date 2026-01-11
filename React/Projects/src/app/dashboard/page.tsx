'use client'

import { supabase } from "@/src/lib/supabase"
import { useRouter } from "next/router" //next.jsの画面遷移

export default function Dashboard() {
    const router = useRouter()

    const handleLogout = async () => {
        const logout = await supabase.auth.signOut({
            email,
            password,
        })
        if (logout){
            router.push('/')
        }
    }
    return (
        <div>
            <button
             onClick={handleLogout}
             >ログアウト</button>
            <p>こんにちは、ゲストさん</p>
        </div>
    )
}