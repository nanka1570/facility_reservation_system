'use client'

import { supabase } from "@/lib/supabase"
import { useRouter } from "next/navigation" //next.jsの画面遷移
import { useEffect, useState } from "react"

export default function Dashboard() {
    const router = useRouter()
    const [ displayName, setDisplayName ] = useState('')

    const handleLogout = async () => {
        const { error } = await supabase.auth.signOut()
        if (!error){
            router.push('/')
        }
    }

    useEffect(() => {
        const checkSession = async () => {
            const { data: { session } } = await supabase.auth.getSession()
            
            if (!session) {
                router.push('/')
            } else {
                const userId = session.user.id
                const { data, error } = await supabase
                    .from('profiles')
                    .select('*')
                    .eq('id', userId)
                    .single()
                setDisplayName(data.display_name)
            }
            
        }
        checkSession()
    }, [])

    return (
        <>
            <div>ダッシュボード</div>
            <div>
                <p>こんにちは、{displayName}さん</p>
                <button
                onClick={handleLogout}
                >ログアウト</button>
            </div>
        </>
    )
}