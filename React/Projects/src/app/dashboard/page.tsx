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
        <div className="bg-gray-100 min-h-screen">
            <header className="bg-white p-4 shadow flex justify-between">
                <h1>
                    施設管理システム
                </h1>
                <div className="flex gap-4">
                    <button
                     className="text-blue-500 px-3 py-1 rounded hover:text-blue-700 hover:bg-gray-200"
                     >
                        予約
                     </button>
                    <button
                     className="text-blue-500 px-3 py-1 rounded hover:text-blue-700 hover:bg-gray-200"
                     >
                        マイページ
                     </button>
                    <button 
                     className="text-blue-500 px-3 py-1 rounded hover:text-blue-700 hover:bg-gray-200"
                     onClick={handleLogout}
                     >
                        ログアウト</button>
                </div>
            </header>
            <main className="p-6">
                <p
                 className="text-xl mb-4"
                 >
                    こんにちは、{displayName}さん
                </p>
                <div className="flex gap-4">
                    <div className="flex-1 bg-white p-4 rounded shadow">
                        <h2 
                         className="font-bold mb-2"
                         >
                            今日の予約
                        </h2>
                        <p 
                         className="text-gray-500"
                         >
                            (予約なし)
                        </p>
                        <button
                         className="text-blue-500 px-3 py-1 rounded hover:text-blue-700 hover:bg-gray-200"
                         >
                            予約カレンダーへ
                        </button>
                    </div>
                    <div className="flex-1 bg-white p-4 rounded shadow">
                        <h2 className="font-bold mb-2"
                         >
                            今月の予約数
                        </h2>
                        <p>0件</p>
                    </div>
                </div>
            </main>
        </div>
    )
}