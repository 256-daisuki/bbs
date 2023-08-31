use mysql::*;
use mysql::prelude::*;

fn main() -> Result<(), Box<dyn std::error::Error>> {
    let url = "mysql://php-login:@localhost:3306/bbs";
    
    // MySQL/MariaDBへの接続
    let pool = Pool::new(url)?;
    
    // クエリの実行
    let mut conn = pool.get_conn()?;
    let query_result: QueryResult = conn.query("SELECT * FROM your_table")?;
    
    // 結果の処理
    for row in query_result {
        let row: (i32, String, String) = from_row(row?);
        println!("{:?}", row);
    }
    
    Ok(())
}
